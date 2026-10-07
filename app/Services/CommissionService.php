<?php

namespace App\Services;

use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * CommissionService — calculates and manages commission records.
 *
 * Commission priority (highest → lowest):
 *   1. Product-specific commission
 *   2. Category commission
 *   3. Global default commission
 *
 * Commission base (configured in settings):
 *   - product_subtotal  (default): quantity × effective_price, no tax/shipping
 *   - subtotal_before_tax
 *   - total
 *
 * All amounts are stored as DECIMAL(12,2). Never use floats for money.
 */
class CommissionService
{
    // -------------------------------------------------------------------------
    // Calculation
    // -------------------------------------------------------------------------

    /**
     * Calculate the commission amount for a single order item.
     *
     * @param  OrderItem  $item
     * @param  Referral   $referral
     * @return array{type: string, rate: float, base: float, amount: float}
     */
    public function calculate(OrderItem $item, Referral $referral): array
    {
        $product  = Product::find($item->product_id);
        $category = $product ? ProductCategory::find($product->category_id) : null;

        [$type, $rate] = $this->resolveCommissionConfig($product, $category);

        // Determine the commissionable base amount
        $base = $this->resolveBase($item);

        // Raw commission
        $commission = $this->applyRate($base, $type, $rate);

        // Apply product cap if set
        if ($product?->commission_cap > 0) {
            $commission = min($commission, (float) $product->commission_cap);
        }

        return [
            'type'   => $type,
            'rate'   => round($rate, 2),
            'base'   => round($base, 2),
            'amount' => round($commission, 2),
        ];
    }

    /**
     * Determine commission type + rate for a product.
     * Priority: product → category → global default.
     *
     * @return array{string, float}
     */
    public function resolveCommissionConfig(?Product $product, ?ProductCategory $category): array
    {
        // 1. Product-level
        if ($product && $product->referral_enabled && $product->commission_type && $product->commission_value > 0) {
            return [$product->commission_type, (float) $product->commission_value];
        }

        // 2. Category-level
        if ($category && $category->commission_type && $category->commission_value > 0) {
            return [$category->commission_type, (float) $category->commission_value];
        }

        // 3. Global default
        $type  = Setting::get(Setting::DEFAULT_COMMISSION_TYPE, 'percentage');
        $value = (float) Setting::get(Setting::DEFAULT_COMMISSION_VALUE, 10);

        return [$type, $value];
    }

    // -------------------------------------------------------------------------
    // Create Commission Record
    // -------------------------------------------------------------------------

    /**
     * Create commission records for all eligible items in an order.
     * Call this AFTER successful payment confirmation.
     *
     * Uses DB::transaction for atomicity.
     * Uses unique constraint on (order_item_id, astrologer_id) for idempotency.
     *
     * @param  Order    $order
     * @param  Referral $referral
     * @return Commission[]
     */
    public function createForOrder(Order $order, Referral $referral): array
    {
        $holdDays    = (int) Setting::get(Setting::COMMISSION_HOLD_DAYS, 7);
        $astrologer  = $referral->astrologer;
        $commissions = [];

        DB::transaction(function () use ($order, $referral, $astrologer, $holdDays, &$commissions) {

            foreach ($order->items as $item) {
                // Skip if commission already exists (idempotency)
                if (Commission::where('order_item_id', $item->id)
                               ->where('astrologer_id', $astrologer->id)
                               ->exists()) {
                    continue;
                }

                $calculated = $this->calculate($item, $referral);

                if ($calculated['amount'] <= 0) {
                    continue;
                }

                $commission = Commission::create([
                    'astrologer_id'     => $astrologer->id,
                    'customer_id'       => $order->user_id,
                    'order_id'          => $order->id,
                    'order_item_id'     => $item->id,
                    'product_id'        => $item->product_id,
                    'referral_id'       => $referral->id,
                    'commission_type'   => $calculated['type'],
                    'commission_rate'   => $calculated['rate'],
                    'order_amount'      => $calculated['base'],
                    'commission_amount' => $calculated['amount'],
                    'status'            => Commission::STATUS_PENDING,
                    // Commission becomes available N days after delivery
                    // For now set it relative to order; update on delivery event
                    'available_at'      => now()->addDays($holdDays),
                ]);

                // Snapshot commission on the order item for historical accuracy
                $item->update([
                    'commission_type'   => $calculated['type'],
                    'commission_value'  => $calculated['rate'],
                    'commission_amount' => $calculated['amount'],
                ]);

                // Add to pending wallet balance
                $wallet = $astrologer->wallet;
                if ($wallet) {
                    $wallet->increment('pending_balance', $calculated['amount']);
                    $wallet->increment('lifetime_earnings', $calculated['amount']);
                }

                $commissions[] = $commission;
            }

            // Mark referral as converted
            $referral->update([
                'order_id'      => $order->id,
                'converted_at'  => now(),
                'status'        => Referral::STATUS_CONVERTED,
            ]);

            // Update order commission status
            $order->update(['commission_status' => 'pending']);
        });

        return $commissions;
    }

    // -------------------------------------------------------------------------
    // Status Transitions
    // -------------------------------------------------------------------------

    /**
     * Move eligible pending commissions to 'available' status and credit wallet.
     * Called by the scheduled command.
     *
     * @return int  Number of commissions released.
     */
    public function releaseEligible(): int
    {
        $released = 0;

        Commission::eligibleForRelease()
            ->with('astrologer.wallet')
            ->each(function (Commission $commission) use (&$released) {

                DB::transaction(function () use ($commission, &$released) {
                    $wallet = $commission->astrologer->wallet;

                    if (!$wallet) {
                        return;
                    }

                    // Idempotency check via WalletTransaction reference
                    $referenceKey = 'COMMISSION-' . $commission->id;
                    if (\App\Models\WalletTransaction::where('reference_key', $referenceKey)->exists()) {
                        return;
                    }

                    $balanceBefore = (float) $wallet->available_balance;
                    $amount        = (float) $commission->commission_amount;

                    // Credit available balance
                    $wallet->increment('available_balance', $amount);
                    $wallet->decrement('pending_balance', $amount);

                    // Ledger entry
                    \App\Models\WalletTransaction::create([
                        'wallet_id'      => $wallet->id,
                        'astrologer_id'  => $commission->astrologer_id,
                        'type'           => \App\Models\WalletTransaction::TYPE_COMMISSION_CREDIT,
                        'reference_type' => 'Commission',
                        'reference_id'   => $commission->id,
                        'reference_key'  => $referenceKey,
                        'amount'         => $amount,
                        'balance_before' => $balanceBefore,
                        'balance_after'  => $balanceBefore + $amount,
                        'description'    => "Commission #{$commission->commission_number} released",
                        'status'         => 'completed',
                    ]);

                    // Update commission status
                    $commission->update([
                        'status'      => Commission::STATUS_AVAILABLE,
                        'approved_at' => now(),
                    ]);

                    $released++;
                });
            });

        return $released;
    }

    /**
     * Admin manually approves a pending commission.
     */
    public function approve(Commission $commission, int $adminId): void
    {
        DB::transaction(function () use ($commission, $adminId) {
            $commission->update([
                'status'      => Commission::STATUS_APPROVED,
                'approved_at' => now(),
                'action_by'   => $adminId,
            ]);
        });
    }

    /**
     * Admin rejects a pending/approved commission.
     */
    public function reject(Commission $commission, int $adminId, string $reason): void
    {
        DB::transaction(function () use ($commission, $adminId, $reason) {
            $wallet = $commission->astrologer->wallet;

            // Reverse pending balance
            if ($wallet && $commission->status === Commission::STATUS_PENDING) {
                $wallet->decrement('pending_balance', (float) $commission->commission_amount);
                $wallet->decrement('lifetime_earnings', (float) $commission->commission_amount);
            }

            $commission->update([
                'status'           => Commission::STATUS_REJECTED,
                'rejected_at'      => now(),
                'rejection_reason' => $reason,
                'action_by'        => $adminId,
            ]);
        });
    }

    /**
     * Cancel a commission (e.g. order cancelled before delivery).
     */
    public function cancel(Commission $commission): void
    {
        DB::transaction(function () use ($commission) {
            if (!in_array($commission->status, [Commission::STATUS_PENDING, Commission::STATUS_APPROVED], true)) {
                return;
            }

            $wallet = $commission->astrologer->wallet;
            if ($wallet) {
                $wallet->decrement('pending_balance', (float) $commission->commission_amount);
                $wallet->decrement('lifetime_earnings', (float) $commission->commission_amount);
            }

            $commission->update(['status' => Commission::STATUS_CANCELLED]);
        });
    }

    /**
     * Reverse an already-available commission (e.g. refund).
     * If the commission was already paid/withdrawn, creates a negative ledger adjustment.
     */
    public function reverse(Commission $commission, ?string $reason = null): void
    {
        DB::transaction(function () use ($commission, $reason) {
            $wallet = $commission->astrologer->wallet;
            $amount = (float) $commission->commission_amount;

            if ($commission->status === Commission::STATUS_AVAILABLE && $wallet) {
                $balanceBefore = (float) $wallet->available_balance;
                $wallet->decrement('available_balance', $amount);
                $wallet->decrement('lifetime_earnings', $amount);

                \App\Models\WalletTransaction::create([
                    'wallet_id'      => $wallet->id,
                    'astrologer_id'  => $commission->astrologer_id,
                    'type'           => \App\Models\WalletTransaction::TYPE_COMMISSION_REVERSAL,
                    'reference_type' => 'Commission',
                    'reference_id'   => $commission->id,
                    'reference_key'  => 'REVERSAL-COMMISSION-' . $commission->id,
                    'amount'         => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after'  => max(0, $balanceBefore - $amount),
                    'description'    => $reason ?? "Commission #{$commission->commission_number} reversed",
                    'status'         => 'reversed',
                ]);
            }

            $commission->update(['status' => Commission::STATUS_CANCELLED]);
        });
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Determine the commissionable base amount from an order item.
     */
    private function resolveBase(OrderItem $item): float
    {
        $base = Setting::get(Setting::COMMISSION_BASE, 'product_subtotal');

        return match ($base) {
            'product_subtotal'    => (float) $item->subtotal,
            // For more advanced bases, we'd need order-level data; default to subtotal
            'subtotal_before_tax' => (float) $item->subtotal,
            'total'               => (float) $item->subtotal,
            default               => (float) $item->subtotal,
        };
    }

    /**
     * Apply a rate to a base amount.
     */
    private function applyRate(float $base, string $type, float $rate): float
    {
        return match ($type) {
            'percentage' => round($base * $rate / 100, 2),
            'fixed'      => round($rate, 2),
            default      => 0.0,
        };
    }
}

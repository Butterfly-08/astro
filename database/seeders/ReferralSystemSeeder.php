<?php

namespace Database\Seeders;

use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\ReferralCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReferralSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Settings
        $defaults = Setting::defaults();
        foreach ($defaults as $item) {
            Setting::firstOrCreate(
                ['key' => $item['key']],
                [
                    'value'       => $item['value'],
                    'group'       => $item['group'] ?? 'general',
                    'type'        => $item['type'] ?? 'string',
                    'label'       => $item['label'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
        }

        // 2. Update Category Commission Defaults
        $categoryCommissions = [
            'gemstones'       => ['type' => 'percentage', 'value' => 10.00],
            'rudraksha'       => ['type' => 'percentage', 'value' => 12.00],
            'yantras'         => ['type' => 'percentage', 'value' => 8.00],
            'puja-essentials' => ['type' => 'percentage', 'value' => 5.00],
            'reports-texts'   => ['type' => 'percentage', 'value' => 15.00],
            'healing-crystals'=> ['type' => 'percentage', 'value' => 10.00],
        ];

        foreach ($categoryCommissions as $slug => $comm) {
            ProductCategory::where('slug', $slug)->update([
                'commission_type'  => $comm['type'],
                'commission_value' => $comm['value'],
            ]);
        }

        // 3. Update Products to enable referral
        Product::query()->update([
            'referral_enabled' => true,
        ]);

        // Specific high-value products commission override example
        Product::where('slug', 'like', '%rudraksha%')->update([
            'commission_type'  => 'percentage',
            'commission_value' => 12.00,
            'commission_cap'   => 1500.00,
        ]);

        Product::where('slug', 'like', '%gemstone%')->orWhere('slug', 'like', '%ruby%')->orWhere('slug', 'like', '%emerald%')->update([
            'commission_type'  => 'percentage',
            'commission_value' => 10.00,
            'commission_cap'   => 2500.00,
        ]);

        // 4. Ensure Astrologers have User accounts, Referral codes, and Wallets
        $referralCodeService = app(ReferralCodeService::class);
        $astrologers = Astrologer::all();

        foreach ($astrologers as $astrologer) {
            // Find or create User
            $user = User::firstOrCreate(
                ['email' => $astrologer->email],
                [
                    'role'              => 'astrologer',
                    'first_name'        => Str::before($astrologer->display_name, ' '),
                    'last_name'         => Str::after($astrologer->display_name, ' ') ?: 'Astrologer',
                    'phone'             => $astrologer->phone ?? '+91 9876543210',
                    'password'          => Hash::make('Password@123'),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Ensure role is astrologer
            if ($user->role !== 'astrologer') {
                $user->update(['role' => 'astrologer']);
            }

            // Assign referral code if missing
            $referralCode = $astrologer->referral_code;
            if (empty($referralCode)) {
                $referralCode = $referralCodeService->generateForAstrologer($astrologer);
            }

            $astrologer->update([
                'user_id'         => $user->id,
                'referral_code'   => $referralCode,
                'approval_status' => 'approved',
                'status'          => 'active',
            ]);

            // Create Wallet if missing
            Wallet::firstOrCreate(
                ['astrologer_id' => $astrologer->id],
                [
                    'available_balance'   => 0.00,
                    'pending_balance'     => 0.00,
                    'held_balance'        => 0.00,
                    'lifetime_earnings'   => 0.00,
                    'lifetime_withdrawn'  => 0.00,
                    'currency'            => 'INR',
                ]
            );
        }

        // 5. Create Demo Astrologer "Anjali Sharma" (Code: ANJALI123)
        $anjaliUser = User::updateOrCreate(
            ['email' => 'anjali@astrovani.test'],
            [
                'role'              => 'astrologer',
                'first_name'        => 'Anjali',
                'last_name'         => 'Sharma',
                'phone'             => '+91 9876543299',
                'password'          => Hash::make('Password@123'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        $anjali = Astrologer::updateOrCreate(
            ['email' => 'anjali@astrovani.test'],
            [
                'user_id'             => $anjaliUser->id,
                'display_name'        => 'Anjali Sharma',
                'slug'                => 'anjali-sharma',
                'phone'               => '+91 9876543299',
                'short_bio'           => 'Expert Tarot and Vedic astrologer helping clients with accurate gemstone and rudraksha recommendations.',
                'bio'                 => 'Acharya Anjali Sharma is a celebrity astrologer with 12+ years of Vedic Jyotish practice. She advises on planetary alignments, gemstones, and remedial yantras.',
                'specializations'     => 'Vedic Astrology, Tarot Reading, Gemology',
                'languages'           => 'Hindi, English',
                'experience_years'    => 12,
                'education'           => 'MA Astrology, Gold Medalist',
                'chat_rate'           => 20.00,
                'call_rate'           => 25.00,
                'video_rate'          => 35.00,
                'rating_avg'          => 4.90,
                'total_reviews'       => 450,
                'total_consultations' => 3200,
                'status'              => 'active',
                'approval_status'     => 'approved',
                'referral_code'       => 'ANJALI123',
                'is_featured'         => true,
                'is_available'        => true,
            ]
        );

        $wallet = Wallet::updateOrCreate(
            ['astrologer_id' => $anjali->id],
            [
                'available_balance'  => 3250.00,
                'pending_balance'    => 750.00,
                'held_balance'       => 0.00,
                'lifetime_earnings'  => 6500.00,
                'lifetime_withdrawn' => 2500.00,
                'currency'           => 'INR',
            ]
        );

        // Add some sample wallet transaction history for Anjali
        if (WalletTransaction::where('astrologer_id', $anjali->id)->count() === 0) {
            WalletTransaction::create([
                'wallet_id'       => $wallet->id,
                'astrologer_id'   => $anjali->id,
                'type'            => WalletTransaction::TYPE_COMMISSION_CREDIT,
                'amount'          => 3250.00,
                'balance_before'  => 0.00,
                'balance_after'   => 3250.00,
                'reference_type'  => 'Commission',
                'reference_id'    => 1,
                'reference_key'   => 'SEED-COMMISSION-1',
                'description'     => 'Commission earned on Gemstone referral sale',
                'status'          => 'completed',
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminWithdrawalApiController extends Controller
{
    public function __construct(
        private readonly WithdrawalService $withdrawalService
    ) {}

    /**
     * Ensure the authenticated user is an Admin.
     */
    private function resolveAdminId(Request $request): int
    {
        $user = $request->user();
        if ($user instanceof Admin) {
            return $user->id;
        }

        // Check fallback or web admin guard
        if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
            return (int) \Illuminate\Support\Facades\Auth::guard('admin')->id();
        }

        return (int) ($user?->id ?? 1);
    }

    /**
     * List all astrologer withdrawal requests with filters and summary metrics.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Withdrawal::with([
            'astrologer:id,user_id,display_name,email,phone,referral_code,approval_status',
            'astrologer.wallet:id,astrologer_id,available_balance,held_balance,pending_balance,lifetime_earnings,lifetime_withdrawn',
            'wallet:id,astrologer_id,available_balance,held_balance',
        ])->latest();

        if ($request->filled('status') && in_array($request->status, Withdrawal::allStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('withdrawal_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('upi_id', 'like', "%{$search}%")
                  ->orWhereHas('astrologer', function ($aq) use ($search) {
                      $aq->where('display_name', 'like', "%{$search}%")
                         ->orWhere('referral_code', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $withdrawals = $query->paginate($perPage);

        $metrics = [
            'total_requests'   => Withdrawal::count(),
            'pending_count'    => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'approved_count'   => Withdrawal::where('status', Withdrawal::STATUS_APPROVED)->count(),
            'processing_count' => Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->count(),
            'paid_count'       => Withdrawal::where('status', Withdrawal::STATUS_PAID)->count(),
            'rejected_count'   => Withdrawal::where('status', Withdrawal::STATUS_REJECTED)->count(),
            'paid_amount'      => (float) Withdrawal::where('status', Withdrawal::STATUS_PAID)->sum('amount'),
            'pending_amount'   => (float) Withdrawal::whereIn('status', [
                Withdrawal::STATUS_PENDING,
                Withdrawal::STATUS_UNDER_REVIEW,
                Withdrawal::STATUS_APPROVED,
                Withdrawal::STATUS_PROCESSING,
            ])->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
            'data'    => $withdrawals,
        ]);
    }

    /**
     * Get single withdrawal request details.
     */
    public function show(int $id): JsonResponse
    {
        $withdrawal = Withdrawal::with([
            'astrologer.user:id,first_name,last_name,email,phone',
            'astrologer.wallet',
            'wallet',
        ])->find($id);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $withdrawal,
        ]);
    }

    /**
     * Admin approves a withdrawal request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::find($id);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found.',
            ], 404);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $adminId = $this->resolveAdminId($request);

        try {
            $this->withdrawalService->approve($withdrawal, $adminId, $validated['notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => "Withdrawal request #{$withdrawal->withdrawal_number} approved successfully.",
                'data'    => $withdrawal->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Admin marks a withdrawal request as processing.
     */
    public function markProcessing(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::find($id);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found.',
            ], 404);
        }

        $adminId = $this->resolveAdminId($request);

        try {
            $this->withdrawalService->markProcessing($withdrawal, $adminId);

            return response()->json([
                'success' => true,
                'message' => "Withdrawal request #{$withdrawal->withdrawal_number} marked as processing.",
                'data'    => $withdrawal->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Admin marks withdrawal as paid with UTR / transaction reference.
     */
    public function markPaid(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::find($id);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found.',
            ], 404);
        }

        $validated = $request->validate([
            'transaction_reference' => ['required', 'string', 'min:4', 'max:100'],
        ]);

        $adminId = $this->resolveAdminId($request);

        try {
            $this->withdrawalService->markPaid($withdrawal, $adminId, $validated['transaction_reference']);

            return response()->json([
                'success' => true,
                'message' => "Withdrawal request #{$withdrawal->withdrawal_number} marked as PAID. Reference: {$validated['transaction_reference']}",
                'data'    => $withdrawal->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Admin rejects withdrawal request and refunds held balance back to astrologer.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $withdrawal = Withdrawal::find($id);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found.',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $adminId = $this->resolveAdminId($request);

        try {
            $this->withdrawalService->reject($withdrawal, $adminId, $validated['reason']);

            return response()->json([
                'success' => true,
                'message' => "Withdrawal request #{$withdrawal->withdrawal_number} rejected. Reserved funds refunded to astrologer balance.",
                'data'    => $withdrawal->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

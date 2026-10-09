<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\AuditLog;
use App\Models\Referral;
use App\Services\ReferralCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::with(['astrologer', 'product', 'order'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('astrologer', function ($aq) use ($search) {
                      $aq->where('display_name', 'like', "%{$search}%");
                  });
            });
        }

        $referrals = $query->paginate(20)->withQueryString();
        $astrologers = Astrologer::orderBy('display_name')->get();

        $metrics = [
            'total_clicks'      => Referral::count(),
            'converted_clicks'  => Referral::where('status', Referral::STATUS_CONVERTED)->count(),
            'expired_clicks'    => Referral::where('status', Referral::STATUS_EXPIRED)->count(),
        ];
        $metrics['conversion_rate'] = $metrics['total_clicks'] > 0
            ? round(($metrics['converted_clicks'] / $metrics['total_clicks']) * 100, 2)
            : 0;

        return view('admin.referrals.index', compact('referrals', 'astrologers', 'metrics'));
    }

    public function partners(Request $request)
    {
        $query = Astrologer::with('wallet')->latest();

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $astrologers = $query->paginate(15)->withQueryString();

        return view('admin.referrals.partners', compact('astrologers'));
    }

    public function updatePartnerStatus(Request $request, Astrologer $astrologer, ReferralCodeService $codeService)
    {
        if ($request->filled('referral_code')) {
            $request->merge(['referral_code' => strtoupper(trim($request->input('referral_code')))]);
        } else {
            $request->merge(['referral_code' => null]);
        }

        $validated = $request->validate([
            'approval_status'    => ['required', 'in:pending,approved,rejected,suspended'],
            'referral_code'      => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9]+$/', 'unique:astrologers,referral_code,' . $astrologer->id],
            'suspension_reason'  => ['nullable', 'string', 'max:255'],
        ], [
            'referral_code.regex' => 'The referral code format is invalid.',
        ], [
            'referral_code' => 'referral code',
        ]);

        $newReferralCode = $validated['referral_code'] ?? $astrologer->referral_code;
        if ($validated['approval_status'] === 'approved' && !$newReferralCode) {
            $newReferralCode = $codeService->generateForAstrologer($astrologer);
        }

        $newSuspensionReason = $validated['approval_status'] === 'suspended'
            ? ($validated['suspension_reason'] ?? 'Suspended by Administrator')
            : null;
        $newAstrologerStatus = $validated['approval_status'] === 'approved'
            ? 'active'
            : $astrologer->status;

        $isNoOp = $astrologer->approval_status === $validated['approval_status']
            && $astrologer->referral_code === $newReferralCode
            && $astrologer->suspension_reason === $newSuspensionReason
            && $astrologer->status === $newAstrologerStatus;

        if ($isNoOp) {
            return back()->with('info', 'No changes were made.');
        }

        $adminId = Auth::guard('admin')->id();
        $oldStatus = $astrologer->approval_status;

        $astrologer->approval_status = $validated['approval_status'];
        $astrologer->referral_code = $newReferralCode;
        $astrologer->suspension_reason = $newSuspensionReason;
        $astrologer->status = $newAstrologerStatus;

        if ($validated['approval_status'] === 'approved' && !$astrologer->approved_at) {
            $astrologer->approved_at = now();
            $astrologer->approved_by = $adminId;
        }

        $astrologer->save();

        AuditLog::record(
            'astrologer_partner_status_updated',
            $astrologer,
            ['approval_status' => $oldStatus],
            ['approval_status' => $astrologer->approval_status, 'code' => $astrologer->referral_code],
            "Astrologer partner status changed to {$astrologer->approval_status}",
            $adminId,
            'Admin'
        );

        return back()->with('success', "Astrologer {$astrologer->display_name} referral status updated to " . ucfirst($astrologer->approval_status));
    }
}

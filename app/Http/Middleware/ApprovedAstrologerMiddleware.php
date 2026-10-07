<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApprovedAstrologerMiddleware — ensures the authenticated astrologer
 * has been approved and is currently active.
 */
class ApprovedAstrologerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('web')->user();

        if (!$user) {
            return redirect()->route('astrologer.login')
                             ->with('error', 'Please log in to continue.');
        }

        // User must have role = 'astrologer'
        if (($user->role ?? null) !== 'astrologer') {
            abort(403, 'Access denied.');
        }

        $astrologer = $user->astrologer;

        if (!$astrologer) {
            abort(403, 'Astrologer profile not found.');
        }

        if ($astrologer->approval_status === \App\Models\Astrologer::APPROVAL_PENDING) {
            return redirect()->route('astrologer.pending')
                             ->with('info', 'Your account is under review.');
        }

        if ($astrologer->approval_status === \App\Models\Astrologer::APPROVAL_REJECTED) {
            return redirect()->route('astrologer.login')
                             ->with('error', 'Your astrologer application was rejected.');
        }

        if ($astrologer->approval_status === \App\Models\Astrologer::APPROVAL_SUSPENDED) {
            return redirect()->route('astrologer.suspended')
                             ->with('error', 'Your account has been suspended.');
        }

        return $next($request);
    }
}

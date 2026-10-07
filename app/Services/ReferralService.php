<?php

namespace App\Services;

use App\Models\Astrologer;
use App\Models\Product;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * ReferralService — handles referral link clicks, cookie storage, attribution.
 *
 * Attribution model: last_click (default) or first_click (configurable).
 * Cookie name: astro_referral_code
 * Cookie duration: configured in settings (default 30 days).
 */
class ReferralService
{
    public const COOKIE_NAME    = 'astro_referral_code';
    public const SESSION_KEY    = 'referral_code';

    public function __construct(
        private readonly ReferralCodeService $codeService
    ) {}

    // -------------------------------------------------------------------------
    // Validate & Track Click
    // -------------------------------------------------------------------------

    /**
     * Validate the referral code, store attribution, and track the click.
     *
     * @param  string       $code
     * @param  Request      $request
     * @param  Product|null $product
     * @return Referral|null  null if code is invalid / system disabled
     */
    public function trackClick(string $code, Request $request, ?Product $product = null): ?Referral
    {
        // Is referral system enabled?
        if (!Setting::get(Setting::REFERRAL_ENABLED, true)) {
            return null;
        }

        $code        = strtoupper(trim($code));
        $astrologer  = $this->codeService->findActiveAstrologer($code);

        if (!$astrologer) {
            return null; // invalid / inactive referral
        }

        // Store attribution in session (and cookie via response)
        $this->storeAttribution($code, $request);

        // Record click in DB
        $referral = Referral::create([
            'astrologer_id' => $astrologer->id,
            'referral_code' => $code,
            'customer_id'   => auth()->id(),
            'session_id'    => $request->session()->getId(),
            'product_id'    => $product?->id,
            'clicked_at'    => now(),
            'status'        => Referral::STATUS_CLICKED,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return $referral;
    }

    // -------------------------------------------------------------------------
    // Attribution Storage
    // -------------------------------------------------------------------------

    /**
     * Store referral code in session.
     * The cookie is queued and must be attached to the HTTP response by the caller.
     */
    public function storeAttribution(string $code, Request $request): void
    {
        $attribution = Setting::get(Setting::REFERRAL_ATTRIBUTION, 'last_click');
        $cookieDays  = (int) Setting::get(Setting::REFERRAL_COOKIE_DAYS, 30);

        // For first_click: only store if no existing attribution
        if ($attribution === 'first_click' && $request->session()->has(self::SESSION_KEY)) {
            return;
        }

        // Store in session
        $request->session()->put(self::SESSION_KEY, $code);

        // Queue cookie (caller must use withCookie() on the response)
        Cookie::queue(
            Cookie::make(
                self::COOKIE_NAME,
                $code,
                $cookieDays * 60 * 24, // in minutes
                '/',
                null,
                false,  // secure — set true in production (HTTPS)
                true,   // httpOnly
                false,
                'lax'   // sameSite
            )
        );
    }

    // -------------------------------------------------------------------------
    // Attribution Retrieval
    // -------------------------------------------------------------------------

    /**
     * Get the referral code currently attributed to this visitor.
     * Session takes priority over cookie.
     *
     * @param  Request $request
     * @return string|null
     */
    public function getAttributedCode(Request $request): ?string
    {
        // Check session first
        $code = $request->session()->get(self::SESSION_KEY);

        if (!$code) {
            // Fall back to cookie
            $code = $request->cookie(self::COOKIE_NAME);
        }

        return $code ? strtoupper($code) : null;
    }

    /**
     * Get the astrologer attributed to the current request, or null.
     */
    public function getAttributedAstrologer(Request $request): ?Astrologer
    {
        $code = $this->getAttributedCode($request);
        if (!$code) {
            return null;
        }
        return $this->codeService->findActiveAstrologer($code);
    }

    // -------------------------------------------------------------------------
    // Self-referral Check
    // -------------------------------------------------------------------------

    /**
     * Return true if the buyer is the same user as the referred astrologer.
     * This prevents self-referral commission fraud.
     */
    public function isSelfReferral(int $buyerUserId, Astrologer $astrologer): bool
    {
        return $astrologer->user_id !== null
            && $astrologer->user_id === $buyerUserId;
    }

    // -------------------------------------------------------------------------
    // Clear Attribution
    // -------------------------------------------------------------------------

    /**
     * Remove referral attribution from session (e.g. after order is placed).
     */
    public function clearAttribution(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    // -------------------------------------------------------------------------
    // Find Latest Referral for Conversion
    // -------------------------------------------------------------------------

    /**
     * Given the current request, find the referral record to convert.
     * Used during checkout to link the order to a referral.
     *
     * @return Referral|null
     */
    public function findClickedReferral(Request $request, int $buyerUserId): ?Referral
    {
        $code = $this->getAttributedCode($request);
        if (!$code) {
            return null;
        }

        $astrologer = $this->codeService->findActiveAstrologer($code);
        if (!$astrologer) {
            return null;
        }

        // Prevent self-referral
        if ($this->isSelfReferral($buyerUserId, $astrologer)) {
            return null;
        }

        // Return the most recent clicked referral for this code/session, or match by IP
        $referral = Referral::where('referral_code', $code)
                       ->where('status', Referral::STATUS_CLICKED)
                       ->where(function ($q) use ($request) {
                           $q->where('session_id', $request->session()->getId())
                             ->orWhere('ip_address', $request->ip());
                       })
                       ->latest()
                       ->first();

        if (!$referral) {
            $referral = Referral::create([
                'astrologer_id' => $astrologer->id,
                'referral_code' => $code,
                'customer_id'   => $buyerUserId,
                'session_id'    => $request->session()->getId(),
                'clicked_at'    => now(),
                'status'        => Referral::STATUS_CLICKED,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
            ]);
        }

        return $referral;
    }
}

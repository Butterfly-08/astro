<?php

namespace App\Services;

use App\Models\Astrologer;
use Illuminate\Support\Str;

/**
 * ReferralCodeService — generates and validates unique referral codes.
 *
 * Format: ASTRO + 4 alphanumeric chars  → e.g. ASTRO7F92
 * Custom prefix can be derived from the astrologer's name: ANJALI7F92
 */
class ReferralCodeService
{
    private const MAX_ATTEMPTS = 20;

    /**
     * Generate a unique referral code for the given astrologer.
     *
     * @param  Astrologer $astrologer
     * @param  bool       $useNamePrefix  Use first 4 letters of name instead of "ASTRO"
     * @return string
     * @throws \RuntimeException
     */
    public function generate(Astrologer $astrologer, bool $useNamePrefix = true): string
    {
        $attempts = 0;

        do {
            $code = $this->buildCode($astrologer->display_name, $useNamePrefix);
            $attempts++;

            if ($attempts > self::MAX_ATTEMPTS) {
                throw new \RuntimeException(
                    "Could not generate a unique referral code after {$attempts} attempts."
                );
            }
        } while ($this->codeExists($code));

        return $code;
    }

    /**
     * Alias for generate()
     */
    public function generateForAstrologer(Astrologer $astrologer, bool $useNamePrefix = true): string
    {
        return $this->generate($astrologer, $useNamePrefix);
    }

    /**
     * Validate a referral code and return the owning astrologer (or null).
     *
     * @param  string $code
     * @return Astrologer|null
     */
    public function findActiveAstrologer(string $code): ?Astrologer
    {
        return Astrologer::where('referral_code', strtoupper(trim($code)))
                         ->where('approval_status', Astrologer::APPROVAL_APPROVED)
                         ->where('status', 'active')
                         ->first();
    }

    /**
     * Check whether a referral code already exists in the database.
     */
    public function codeExists(string $code): bool
    {
        return Astrologer::where('referral_code', $code)->exists();
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function buildCode(string $name, bool $useNamePrefix): string
    {
        $prefix = $useNamePrefix
            ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 5))
            : 'ASTRO';

        // Ensure prefix is at least 3 chars
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'A');
        }

        // Append 4 random alphanumeric chars
        $suffix = strtoupper(Str::random(4));

        return $prefix . $suffix;
    }
}

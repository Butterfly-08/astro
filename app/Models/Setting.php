<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Setting — admin-configurable key/value store.
 * Values are cached for performance; cache cleared on save/update.
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    // -------------------------------------------------------------------------
    // Keys (constants)
    // -------------------------------------------------------------------------

    // Commission
    public const DEFAULT_COMMISSION_TYPE  = 'default_commission_type';  // percentage|fixed
    public const DEFAULT_COMMISSION_VALUE = 'default_commission_value'; // e.g. 10
    public const COMMISSION_BASE          = 'commission_base';          // product_subtotal|total
    public const COMMISSION_HOLD_DAYS     = 'commission_hold_days';     // e.g. 7

    // Referral
    public const REFERRAL_ENABLED         = 'referral_enabled';         // true|false
    public const REFERRAL_COOKIE_DAYS     = 'referral_cookie_days';     // e.g. 30
    public const REFERRAL_ATTRIBUTION     = 'referral_attribution';     // first_click|last_click

    // Withdrawal
    public const WITHDRAWAL_MIN_AMOUNT    = 'withdrawal_min_amount';    // e.g. 500
    public const WITHDRAWAL_MAX_AMOUNT    = 'withdrawal_max_amount';    // e.g. 50000
    public const WITHDRAWAL_FEE_TYPE      = 'withdrawal_fee_type';      // percentage|fixed
    public const WITHDRAWAL_FEE_VALUE     = 'withdrawal_fee_value';     // e.g. 2 or 20

    // -------------------------------------------------------------------------
    // Boot — clear cache on change
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::saved(function (Setting $setting) {
            Cache::forget('setting_' . $setting->key);
            Cache::forget('settings_all');
        });
    }

    // -------------------------------------------------------------------------
    // Static Helpers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a setting value by key, with a default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }

    /**
     * Cast the raw string value to the appropriate PHP type.
     */
    private static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    // -------------------------------------------------------------------------
    // Default Values (used by SettingsSeeder)
    // -------------------------------------------------------------------------

    public static function defaults(): array
    {
        return [
            [
                'key'         => self::REFERRAL_ENABLED,
                'value'       => 'true',
                'type'        => 'boolean',
                'group'       => 'referral',
                'label'       => 'Referral System Enabled',
                'description' => 'Enable or disable the entire referral system.',
            ],
            [
                'key'         => self::REFERRAL_COOKIE_DAYS,
                'value'       => '30',
                'type'        => 'integer',
                'group'       => 'referral',
                'label'       => 'Referral Cookie Duration (days)',
                'description' => 'How many days a referral cookie remains valid.',
            ],
            [
                'key'         => self::REFERRAL_ATTRIBUTION,
                'value'       => 'last_click',
                'type'        => 'string',
                'group'       => 'referral',
                'label'       => 'Attribution Model',
                'description' => 'first_click = original referrer, last_click = most recent.',
            ],
            [
                'key'         => self::DEFAULT_COMMISSION_TYPE,
                'value'       => 'percentage',
                'type'        => 'string',
                'group'       => 'commission',
                'label'       => 'Default Commission Type',
                'description' => 'percentage or fixed.',
            ],
            [
                'key'         => self::DEFAULT_COMMISSION_VALUE,
                'value'       => '10',
                'type'        => 'decimal',
                'group'       => 'commission',
                'label'       => 'Default Commission Value',
                'description' => 'Percentage (e.g. 10) or fixed amount (e.g. 50).',
            ],
            [
                'key'         => self::COMMISSION_BASE,
                'value'       => 'product_subtotal',
                'type'        => 'string',
                'group'       => 'commission',
                'label'       => 'Commission Base',
                'description' => 'product_subtotal | subtotal_before_tax | total.',
            ],
            [
                'key'         => self::COMMISSION_HOLD_DAYS,
                'value'       => '7',
                'type'        => 'integer',
                'group'       => 'commission',
                'label'       => 'Commission Hold Period (days)',
                'description' => 'Days after order delivery before commission becomes available.',
            ],
            [
                'key'         => self::WITHDRAWAL_MIN_AMOUNT,
                'value'       => '500',
                'type'        => 'decimal',
                'group'       => 'withdrawal',
                'label'       => 'Minimum Withdrawal Amount (₹)',
                'description' => 'Minimum amount an astrologer can request.',
            ],
            [
                'key'         => self::WITHDRAWAL_MAX_AMOUNT,
                'value'       => '50000',
                'type'        => 'decimal',
                'group'       => 'withdrawal',
                'label'       => 'Maximum Withdrawal Amount (₹)',
                'description' => 'Maximum amount per single withdrawal request.',
            ],
            [
                'key'         => self::WITHDRAWAL_FEE_TYPE,
                'value'       => 'percentage',
                'type'        => 'string',
                'group'       => 'withdrawal',
                'label'       => 'Withdrawal Fee Type',
                'description' => 'percentage or fixed.',
            ],
            [
                'key'         => self::WITHDRAWAL_FEE_VALUE,
                'value'       => '0',
                'type'        => 'decimal',
                'group'       => 'withdrawal',
                'label'       => 'Withdrawal Fee Value',
                'description' => 'Fee percentage (e.g. 2) or fixed fee in ₹ (e.g. 20). Set 0 for no fee.',
            ],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AuditLog — immutable record of significant actions.
 * Never update or delete rows from this table.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null; // no updated_at column

    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Action Constants
    // -------------------------------------------------------------------------

    public const ASTROLOGER_APPROVED   = 'astrologer.approved';
    public const ASTROLOGER_REJECTED   = 'astrologer.rejected';
    public const ASTROLOGER_SUSPENDED  = 'astrologer.suspended';
    public const COMMISSION_APPROVED   = 'commission.approved';
    public const COMMISSION_REJECTED   = 'commission.rejected';
    public const COMMISSION_AVAILABLE  = 'commission.available';
    public const WITHDRAWAL_REQUESTED  = 'withdrawal.requested';
    public const WITHDRAWAL_APPROVED   = 'withdrawal.approved';
    public const WITHDRAWAL_REJECTED   = 'withdrawal.rejected';
    public const WITHDRAWAL_PAID       = 'withdrawal.paid';
    public const WALLET_ADJUSTED       = 'wallet.adjusted';
    public const ADMIN_LOGIN           = 'admin.login';
    public const ADMIN_LOGOUT          = 'admin.logout';

    // -------------------------------------------------------------------------
    // Static Helper
    // -------------------------------------------------------------------------

    /**
     * Create an audit log entry conveniently from anywhere in the application.
     */
    public static function record(
        string $action,
        ?Model $model = null,
        array  $old = [],
        array  $new = [],
        ?string $description = null,
        ?int   $userId = null,
        ?string $userType = null
    ): self {
        return static::create([
            'user_id'    => $userId ?? auth()->id(),
            'user_type'  => $userType ?? (auth()->check() ? class_basename(auth()->user()) : null),
            'action'     => $action,
            'description'=> $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int $astrologer_id
 * @property int|null $service_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $booking_date
 * @property string $start_time
 * @property string $end_time
 * @property string|null $cancellation_reason
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'astrologer_id',
        'service_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'consultation_type',
        'rate_per_minute',
        'amount',
        'payment_status',
        'status',
        'notes',
        'admin_notes',
        'cancellation_reason',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'booking_date'     => 'date',
        'cancelled_at'     => 'datetime',
        'completed_at'     => 'datetime',
        'rate_per_minute'  => 'decimal:2',
        'amount'           => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(AstrologerReview::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('booking_date', '>', now()->toDateString())
              ->orWhere(function ($q2) {
                  $q2->where('booking_date', now()->toDateString())
                     ->where('end_time', '>=', now()->toTimeString());
              });
        })->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('booking_date', '<', now()->toDateString())
              ->orWhere(function ($q2) {
                  $q2->where('booking_date', now()->toDateString())
                     ->where('end_time', '<', now()->toTimeString());
              });
        })->orWhereIn('status', ['completed', 'cancelled', 'rejected']);
    }

    // -------------------------------------------------------------------------
    // Helpers & Accessors
    // -------------------------------------------------------------------------

    /**
     * Check if appointment can be cancelled by user.
     */
    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }

        $bookingDateTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        return $bookingDateTime->isFuture();
    }

    /**
     * Formatted time slot display (e.g. 10:00 AM – 10:30 AM).
     */
    public function getFormattedTimeSlotAttribute(): string
    {
        $start = Carbon::parse($this->start_time)->format('h:i A');
        $end   = Carbon::parse($this->end_time)->format('h:i A');
        return "{$start} – {$end}";
    }

    /**
     * Status badge HTML helper for Blade views.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'confirmed' => '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Confirmed</span>',
            'pending'   => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-clock me-1"></i>Pending</span>',
            'completed' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-patch-check-fill me-1"></i>Completed</span>',
            'cancelled' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-x-circle me-1"></i>Cancelled</span>',
            'rejected'  => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-slash-circle me-1"></i>Rejected</span>',
            default     => '<span class="badge bg-light text-dark">Unknown</span>',
        };
    }

    /**
     * Payment badge HTML helper.
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match($this->payment_status) {
            'paid'     => '<span class="badge bg-success"><i class="bi bi-check2 me-1"></i>Paid</span>',
            'pending'  => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>',
            'failed'   => '<span class="badge bg-danger">Failed</span>',
            'refunded' => '<span class="badge bg-info text-dark">Refunded</span>',
            default    => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Generate unique booking number.
     */
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('booking_number', $number)->exists());

        return $number;
    }
}

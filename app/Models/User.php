<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'date_of_birth',
        'gender',
        'city',
        'state',
        'country',
        'profile_image',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''))
        );
    }

    /**
     * Backward-compatible name accessor.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name
        );
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * User's consultation bookings.
     */
    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    /**
     * User's e-commerce orders.
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * User's wishlisted products.
     */
    public function wishlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wishlist::class)->latest();
    }
}

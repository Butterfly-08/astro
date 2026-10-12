<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horoscope extends Model
{
    public const ZODIAC_SIGNS = [
        'Aries',
        'Taurus',
        'Gemini',
        'Cancer',
        'Leo',
        'Virgo',
        'Libra',
        'Scorpio',
        'Sagittarius',
        'Capricorn',
        'Aquarius',
        'Pisces',
    ];

    protected $fillable = [
        'zodiac_sign',
        'horoscope_date',
        'title',
        'content',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'horoscope_date' => 'date',
        ];
    }
}

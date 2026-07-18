<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'activity_type',
        'day_type',
        'price_per_hour',
        'effective_from',
        'effective_until',
        'is_active',
    ];

    protected $casts = [
        'price_per_hour' => 'integer',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'is_active' => 'boolean',
    ];
}

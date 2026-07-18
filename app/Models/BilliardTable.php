<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BilliardTable extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'table_number',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'table_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'type',
        'name',
        'bank_name',
        'account_number',
        'account_holder',
        'qris_image_path',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }
}

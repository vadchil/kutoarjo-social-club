<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'booking_code',
        'billiard_table_id',
        'payment_method_id',
        'customer_name',
        'customer_phone',
        'booking_type',
        'start_at',
        'end_at',
        'duration_minutes',
        'hourly_price',
        'total_price',
        'status',
        'payment_status',
        'expires_at',
        'confirmed_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'customer_notes',
        'admin_notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'hourly_price' => 'integer',
        'total_price' => 'integer',
    ];

    public function billiardTable(): BelongsTo
    {
        return $this->belongsTo(BilliardTable::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }
}

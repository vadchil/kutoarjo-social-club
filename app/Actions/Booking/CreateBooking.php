<?php

namespace App\Actions\Booking;

use App\Actions\CalculateBookingPrice;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateBooking
{
    public function __construct(
        private ValidateBookingAvailability $availabilityValidator,
        private CalculateBookingPrice $priceCalculator
    ) {}

    /**
     * Create a new booking transactionally.
     *
     * @throws \InvalidArgumentException|\Throwable
     */
    final public function execute(array $data, ?string $actorId = null): Booking
    {
        $billiardTableId = $data['billiard_table_id'];
        $startAt = $data['start_at'];
        $endAt = $data['end_at'];
        $bookingType = $data['booking_type'] ?? 'online';

        return DB::transaction(function () use ($data, $billiardTableId, $startAt, $endAt, $bookingType, $actorId) {
            $this->availabilityValidator->execute($billiardTableId, $startAt, $endAt, $bookingType);

            $pricing = $this->priceCalculator->execute('billiard', $startAt, $endAt);

            $datePrefix = Carbon::parse($startAt)->format('ymd');
            do {
                $code = sprintf('KSC-BL-%s-%s', $datePrefix, strtoupper(Str::random(8)));
            } while (Booking::where('booking_code', $code)->exists());

            $expirySetting = SiteSetting::where('key', 'booking_expiry_minutes')->first();
            $expiryMinutes = (int) ($expirySetting?->value['value'] ?? 15);
            $expiryMinutes = max(1, $expiryMinutes);
            $expiresAt = $bookingType === 'online' ? Carbon::now()->addMinutes($expiryMinutes) : null;
            $status = $bookingType === 'walk_in' ? 'in_progress' : 'pending_payment';
            $paymentStatus = 'unpaid';

            $booking = Booking::create([
                'booking_code' => $code,
                'billiard_table_id' => $billiardTableId,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'booking_type' => $bookingType,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $pricing['duration_minutes'],
                'hourly_price' => $pricing['hourly_price'],
                'total_price' => $pricing['total_price'],
                'status' => $status,
                'payment_status' => $paymentStatus,
                'expires_at' => $expiresAt,
                'customer_notes' => $data['customer_notes'] ?? null,
                'created_by' => $actorId,
            ]);

            BookingHistory::create([
                'booking_id' => $booking->id,
                'event_type' => 'booking_created',
                'previous_status' => null,
                'new_status' => $status,
                'actor_type' => $actorId ? 'user' : 'customer',
                'actor_id' => $actorId,
                'notes' => 'Booking created.',
                'metadata' => [
                    'duration_minutes' => $pricing['duration_minutes'],
                    'total_price' => $pricing['total_price'],
                ],
                'created_at' => Carbon::now(),
            ]);

            return $booking;
        });
    }
}

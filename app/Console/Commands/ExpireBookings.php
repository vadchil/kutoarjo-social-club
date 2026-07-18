<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Expire unpaid bookings past expires_at';

    /**
     * Execute the console command.
     */
    final public function handle(): int
    {
        $now = Carbon::now();

        $expiredBookings = Booking::query()
            ->where('status', 'pending_payment')
            ->where('expires_at', '<', $now)
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');

            return 0;
        }

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking, $now, &$count) {
                $oldStatus = $booking->status;

                $booking->update([
                    'status' => 'expired',
                ]);

                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'event_type' => 'status_expired',
                    'previous_status' => $oldStatus,
                    'new_status' => 'expired',
                    'actor_type' => 'system',
                    'actor_id' => null,
                    'notes' => 'Booking expired automatically due to payment timeout.',
                    'created_at' => $now,
                ]);

                $count++;
            });
        }

        $this->info("Successfully expired {$count} booking(s).");

        return 0;
    }
}

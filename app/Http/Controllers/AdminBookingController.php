<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBooking;
use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminBookingController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->input('status');
        $query = Booking::with(['billiardTable', 'paymentMethod'])->orderBy('start_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $query->paginate(15)->withQueryString(),
            'filters' => ['status' => $status],
        ]);
    }

    public function schedule(Request $request): Response
    {
        $dateStr = $request->input('date', Carbon::today()->toDateString());
        $tzSetting = SiteSetting::where('key', 'timezone')->first();
        $tz = ($tzSetting && isset($tzSetting->value['value'])) ? $tzSetting->value['value'] : 'Asia/Jakarta';

        $date = Carbon::parse($dateStr, $tz);

        $bookings = Booking::with('billiardTable')
            ->whereDate('start_at', $date->toDateString())
            ->whereNotIn('status', ['cancelled', 'expired'])
            ->get()
            ->map(fn (Booking $b) => [
                'id' => $b->id,
                'booking_code' => $b->booking_code,
                'billiard_table_id' => $b->billiard_table_id,
                'customer_name' => $b->customer_name,
                'start_time' => $b->start_at->setTimezone($tz)->format('H:i'),
                'end_time' => $b->end_at->setTimezone($tz)->format('H:i'),
                'status' => $b->status,
            ]);

        return Inertia::render('Admin/Schedule', [
            'tables' => BilliardTable::where('is_active', true)->orderBy('table_number')->get(),
            'bookings' => $bookings,
            'selectedDate' => $date->toDateString(),
        ]);
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:confirmed,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking = Booking::findOrFail($id);
        $newStatus = $request->input('status');
        $oldStatus = $booking->status;

        $allowedTransitions = [
            'pending_payment' => ['confirmed', 'cancelled'],
            'waiting_confirmation' => ['confirmed', 'cancelled'],
            'confirmed' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed'],
        ];

        abort_unless(in_array($newStatus, $allowedTransitions[$oldStatus] ?? [], true), 422, 'Invalid booking status transition.');

        $actor = $request->user();

        DB::transaction(function () use ($booking, $newStatus, $oldStatus, $actor, $request) {
            $updateData = ['status' => $newStatus];

            if ($newStatus === 'confirmed') {
                $updateData['confirmed_at'] = now();
                $updateData['payment_status'] = 'paid';
            } elseif ($newStatus === 'in_progress') {
                $updateData['started_at'] = now();
            } elseif ($newStatus === 'completed') {
                $updateData['completed_at'] = now();
                $updateData['payment_status'] = 'paid';
            } elseif ($newStatus === 'cancelled') {
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = $request->input('notes');
            }

            $booking->update($updateData);

            BookingHistory::create([
                'booking_id' => $booking->id,
                'event_type' => "status_{$newStatus}",
                'previous_status' => $oldStatus,
                'new_status' => $newStatus,
                'actor_type' => 'user',
                'actor_id' => $actor->id,
                'notes' => $request->input('notes'),
                'created_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    public function storeWalkIn(Request $request, CreateBooking $createBooking): RedirectResponse
    {
        $request->validate([
            'billiard_table_id' => ['required', 'exists:billiard_tables,id'],
            'duration' => ['required', 'integer', 'min:1', 'max:4'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_notes' => ['nullable', 'string'],
        ]);

        $tzSetting = SiteSetting::where('key', 'timezone')->first();
        $tz = ($tzSetting && isset($tzSetting->value['value'])) ? $tzSetting->value['value'] : 'Asia/Jakarta';

        $startAt = Carbon::now($tz);

        // Apply walk-in rounding logic if configured in settings
        $roundingSetting = SiteSetting::where('key', 'walk_in_rounding_rule')->first();
        $roundingRule = ($roundingSetting && isset($roundingSetting->value['value'])) ? $roundingSetting->value['value'] : 'nearest_15_minutes';

        if ($roundingRule === 'nearest_15_minutes') {
            $minute = $startAt->minute;
            if ($minute < 8) {
                $startAt->minute(0)->second(0);
            } elseif ($minute < 23) {
                $startAt->minute(15)->second(0);
            } elseif ($minute < 38) {
                $startAt->minute(30)->second(0);
            } elseif ($minute < 53) {
                $startAt->minute(45)->second(0);
            } else {
                $startAt->addHour()->minute(0)->second(0);
            }
        }

        $endAt = $startAt->copy()->addHours((int) $request->input('duration'));

        $booking = $createBooking->execute([
            'billiard_table_id' => $request->input('billiard_table_id'),
            'customer_name' => $request->input('customer_name') ?? 'Walk-in Customer',
            'customer_phone' => $request->input('customer_phone') ?? 'Walk-in',
            'booking_type' => 'walk_in',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'customer_notes' => $request->input('customer_notes'),
        ], $request->user()->id);

        return redirect()->back()->with('success', 'Walk-in session started successfully.');
    }
}

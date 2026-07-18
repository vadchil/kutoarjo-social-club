<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CreateBooking;
use App\Actions\Booking\ValidateBookingAvailability;
use App\Actions\CalculateBookingPrice;
use App\Http\Requests\StoreBookingRequest;
use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicBookingController extends Controller
{
    public function create(): Response
    {
        $tzSetting = SiteSetting::where('key', 'timezone')->first();
        $tz = ($tzSetting && isset($tzSetting->value['value'])) ? $tzSetting->value['value'] : 'Asia/Jakarta';

        $maxDaysSetting = SiteSetting::where('key', 'maximum_booking_days')->first();
        $maxDays = ($maxDaysSetting && isset($maxDaysSetting->value['value'])) ? (int) $maxDaysSetting->value['value'] : 14;

        return Inertia::render('Public/Booking', [
            'tables' => BilliardTable::where('is_active', true)->orderBy('table_number')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', true)->orderBy('sort_order')->get(),
            'settings' => [
                'timezone' => $tz,
                'maxDays' => $maxDays,
                'minDuration' => (int) (SiteSetting::where('key', 'minimum_booking_duration')->first()->value['value'] ?? 1),
                'maxDuration' => (int) (SiteSetting::where('key', 'maximum_booking_duration')->first()->value['value'] ?? 4),
            ],
        ]);
    }

    public function checkAvailability(
        Request $request,
        ValidateBookingAvailability $availabilityValidator,
        CalculateBookingPrice $priceCalculator
    ): JsonResponse {
        $validated = $request->validate([
            'billiard_table_id' => ['required', 'exists:billiard_tables,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'booking_type' => ['nullable', 'string'],
        ]);

        try {
            $availabilityValidator->execute(
                $validated['billiard_table_id'],
                $validated['start_at'],
                $validated['end_at'],
                'online'
            );

            $pricing = $priceCalculator->execute('billiard', $validated['start_at'], $validated['end_at']);

            return response()->json([
                'available' => true,
                'price' => $pricing,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'available' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store(StoreBookingRequest $request, CreateBooking $createBooking): RedirectResponse
    {
        $booking = $createBooking->execute($request->validated());

        return redirect()->route('bookings.success', $booking->booking_code);
    }

    public function success(string $bookingCode): Response
    {
        $booking = Booking::with(['billiardTable', 'paymentMethod'])
            ->where('booking_code', $bookingCode)
            ->firstOrFail();

        $waNumberSetting = SiteSetting::where('key', 'whatsapp_number')->first();
        $waNumber = ($waNumberSetting && isset($waNumberSetting->value['value'])) ? $waNumberSetting->value['value'] : '6281234567890';

        $waText = "Halo Kutoarjo Social Club,\n\nSaya ingin mengonfirmasi booking billiard berikut:\n\n";
        $waText .= "Kode booking: {$booking->booking_code}\n";
        $waText .= "Nama: {$booking->customer_name}\n";
        $waText .= "Meja: {$booking->billiardTable->name}\n";
        $waText .= "Tanggal: {$booking->start_at->format('d-m-Y')}\n";
        $waText .= "Waktu: {$booking->start_at->format('H:i')}–{$booking->end_at->format('H:i')} WIB\n";
        $waText .= 'Durasi: '.($booking->duration_minutes / 60)." jam\n";
        $waText .= 'Total: Rp'.number_format($booking->total_price, 0, ',', '.')."\n";
        $waText .= 'Metode pembayaran: '.($booking->paymentMethod ? $booking->paymentMethod->name : 'N/A')."\n\n";
        $waText .= 'Saya akan mengirimkan bukti pembayaran melalui chat ini.';

        $waUrl = "https://api.whatsapp.com/send?phone={$waNumber}&text=".rawurlencode($waText);

        return Inertia::render('Public/BookingSuccess', [
            'booking' => [
                'booking_code' => $booking->booking_code,
                'customer_name' => $booking->customer_name,
                'table_name' => $booking->billiardTable->name,
                'start_at' => $booking->start_at->toIso8601String(),
                'end_at' => $booking->end_at->toIso8601String(),
                'duration_hours' => $booking->duration_minutes / 60,
                'hourly_price' => $booking->hourly_price,
                'total_price' => $booking->total_price,
                'payment_method' => $booking->paymentMethod,
                'status' => $booking->status,
                'expires_at' => $booking->expires_at ? $booking->expires_at->toIso8601String() : null,
            ],
            'whatsapp_url' => $waUrl,
        ]);
    }

    public function lookup(): Response
    {
        return Inertia::render('Public/BookingLookup');
    }

    public function search(Request $request): Response|RedirectResponse
    {
        $request->validate([
            'booking_code' => ['required', 'string'],
            'customer_phone' => ['required', 'string'],
        ]);

        $booking = Booking::with(['billiardTable', 'paymentMethod'])
            ->where('booking_code', $request->input('booking_code'))
            ->where('customer_phone', $request->input('customer_phone'))
            ->first();

        if (! $booking) {
            return Inertia::render('Public/BookingLookup', [
                'error' => 'Booking not found with matching details.',
            ]);
        }

        return Inertia::render('Public/BookingStatus', [
            'booking' => [
                'booking_code' => $booking->booking_code,
                'customer_name' => $booking->customer_name,
                'table_name' => $booking->billiardTable->name,
                'start_at' => $booking->start_at->toIso8601String(),
                'end_at' => $booking->end_at->toIso8601String(),
                'duration_hours' => $booking->duration_minutes / 60,
                'total_price' => $booking->total_price,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'payment_method' => $booking->paymentMethod,
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\BilliardTable;
use App\Models\Booking;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\PaymentMethod;
use App\Models\PricingRule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('SEED_ADMIN_EMAIL', 'admin@kutoarjosocialclub.com');
        $adminPassword = env('SEED_ADMIN_PASSWORD');
        $staffEmail = env('SEED_STAFF_EMAIL', 'staff@kutoarjosocialclub.com');
        $staffPassword = env('SEED_STAFF_PASSWORD');

        if (app()->isProduction() && (! $adminPassword || ! $staffPassword)) {
            throw new RuntimeException('Set SEED_ADMIN_PASSWORD and SEED_STAFF_PASSWORD before seeding production.');
        }

        // 1. Seed Users (Admin & Staff)
        $admin = User::factory()->create([
            'name' => 'Admin KSC',
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword ?: 'password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $staff = User::factory()->create([
            'name' => 'Staff KSC',
            'email' => $staffEmail,
            'password' => Hash::make($staffPassword ?: 'password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        // 2. Seed Billiard Tables (8 tables)
        $tables = [];
        for ($i = 1; $i <= 8; $i++) {
            $tables[] = BilliardTable::factory()->create([
                'table_number' => $i,
                'name' => "Billiard Table {$i}",
                'description' => "Standard KSC billiard table number {$i}",
                'is_active' => true,
            ]);
        }

        // 3. Seed Payment Methods (Transfer Bank, QRIS, Pay at Venue)
        $pmBank = PaymentMethod::factory()->create([
            'type' => 'bank_transfer',
            'name' => 'Transfer Bank Mandiri',
            'bank_name' => env('KSC_BANK_NAME', 'Bank Mandiri'),
            'account_number' => env('KSC_BANK_ACCOUNT', '1234567890'),
            'account_holder' => env('KSC_BANK_HOLDER', 'KUTOARJO SOCIAL CLUB'),
            'instructions' => "Transfer sesuai nominal total booking ke rekening Mandiri KSC.\nKonfirmasi bukti pembayaran melalui WhatsApp.",
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $pmQris = PaymentMethod::factory()->create([
            'type' => 'qris',
            'name' => 'QRIS KSC',
            'qris_image_path' => 'payment_methods/qris_placeholder.png',
            'instructions' => "Scan QRIS KSC menggunakan e-wallet atau mobile banking Anda.\nKonfirmasi bukti pembayaran melalui WhatsApp.",
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $pmCash = PaymentMethod::factory()->create([
            'type' => 'pay_at_venue',
            'name' => 'Bayar di Tempat (Cash / Debit)',
            'instructions' => 'Bayar langsung di kasir Kutoarjo Social Club saat kedatangan.',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // 4. Seed Pricing Rules
        PricingRule::factory()->create([
            'activity_type' => 'padel',
            'day_type' => 'weekday',
            'price_per_hour' => 160000,
            'effective_from' => now()->subYear(),
            'is_active' => true,
        ]);

        PricingRule::factory()->create([
            'activity_type' => 'padel',
            'day_type' => 'weekend',
            'price_per_hour' => 200000,
            'effective_from' => now()->subYear(),
            'is_active' => true,
        ]);

        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekday',
            'price_per_hour' => 15000,
            'effective_from' => now()->subYear(),
            'is_active' => true,
        ]);

        PricingRule::factory()->create([
            'activity_type' => 'billiard',
            'day_type' => 'weekend',
            'price_per_hour' => 20000,
            'effective_from' => now()->subYear(),
            'is_active' => true,
        ]);

        // 5. Seed Site Settings
        $settings = [
            'business_name' => ['name' => 'KSC', 'value' => 'Kutoarjo Social Club'],
            'business_description' => ['name' => 'Description', 'value' => 'Padel Court & Billiard Social Club located in Kutoarjo'],
            'whatsapp_number' => ['name' => 'WhatsApp Number', 'value' => '6281234567890'],
            'instagram_url' => ['name' => 'Instagram URL', 'value' => 'https://instagram.com/kutoarjosocialclub'],
            'ayo_booking_url' => ['name' => 'AYO Booking URL', 'value' => 'https://ayo.co.id/venues/kutoarjo-social-club'],
            'google_maps_url' => ['name' => 'Google Maps URL', 'value' => 'https://maps.google.com/?q=Kutoarjo+Social+Club'],
            'business_address' => ['name' => 'Business Address', 'value' => 'Jl. Pahlawan No. 45, Kutoarjo, Jawa Tengah'],
            'operational_hours' => ['name' => 'Operational Hours', 'value' => '09.00 - 24.00 WIB'],
            'timezone' => ['name' => 'Timezone', 'value' => 'Asia/Jakarta'],
            'booking_expiry_minutes' => ['name' => 'Booking Expiry Minutes', 'value' => 15],
            'maximum_booking_days' => ['name' => 'Maximum Booking Days', 'value' => 14],
            'minimum_booking_duration' => ['name' => 'Minimum Booking Duration (Hours)', 'value' => 1],
            'maximum_booking_duration' => ['name' => 'Maximum Booking Duration (Hours)', 'value' => 4],
            'walk_in_rounding_rule' => ['name' => 'Walk-in Rounding Rule', 'value' => 'nearest_15_minutes'],
        ];

        foreach ($settings as $key => $data) {
            SiteSetting::factory()->create([
                'key' => $key,
                'value' => $data,
                'description' => "System setting for {$data['name']}",
                'is_public' => true,
                'updated_by' => $admin->id,
            ]);
        }

        // 6. Seed demo Galleries (categories: padel, billiard, venue)
        if (! app()->isProduction() || env('SEED_DEMO_DATA', false)) {
            Gallery::factory()->count(3)->create([
                'category' => 'padel',
                'created_by' => $admin->id,
            ]);
            Gallery::factory()->count(3)->create([
                'category' => 'billiard',
                'created_by' => $admin->id,
            ]);
            Gallery::factory()->count(2)->create([
                'category' => 'venue',
                'created_by' => $admin->id,
            ]);
        }

        // 7. Seed FAQs (categories: general, padel, billiard)
        Faq::factory()->create([
            'question' => 'Bagaimana cara memesan lapangan Padel?',
            'answer' => 'Pemesanan lapangan padel dilakukan langsung melalui aplikasi AYO menggunakan link yang tertera di website kami.',
            'category' => 'padel',
            'created_by' => $admin->id,
        ]);

        Faq::factory()->create([
            'question' => 'Apakah bisa memesan meja billiard online?',
            'answer' => 'Ya, Anda bisa memesan meja billiard online melalui website ini. Silakan pilih meja, tanggal, dan waktu bermain Anda.',
            'category' => 'billiard',
            'created_by' => $admin->id,
        ]);

        Faq::factory()->create([
            'question' => 'Berapa durasi minimum booking meja billiard?',
            'answer' => 'Durasi minimum booking meja billiard adalah 1 jam, dan maksimal adalah 4 jam.',
            'category' => 'billiard',
            'created_by' => $admin->id,
        ]);

        Faq::factory()->create([
            'question' => 'Metode pembayaran apa saja yang didukung?',
            'answer' => 'Kami mendukung pembayaran melalui transfer bank manual, QRIS, dan bayar di tempat (cash/debit) jika diaktifkan.',
            'category' => 'general',
            'created_by' => $admin->id,
        ]);

        // 8. Seed Bookings
        // Past booking
        Booking::factory()->create([
            'booking_code' => 'KSC-BL-260710-PST1',
            'billiard_table_id' => $tables[0]->id,
            'payment_method_id' => $pmBank->id,
            'customer_name' => 'Past Customer',
            'customer_phone' => '6281111111111',
            'booking_type' => 'online',
            'start_at' => now()->subDays(5)->setHour(10)->setMinute(0)->setSecond(0),
            'end_at' => now()->subDays(5)->setHour(12)->setMinute(0)->setSecond(0),
            'duration_minutes' => 120,
            'status' => 'completed',
            'payment_status' => 'paid',
            'created_by' => null,
        ]);

        // Active booking today (confirmed)
        Booking::factory()->create([
            'booking_code' => 'KSC-BL-260718-ACT1',
            'billiard_table_id' => $tables[1]->id,
            'payment_method_id' => $pmQris->id,
            'customer_name' => 'Active Customer',
            'customer_phone' => '6282222222222',
            'booking_type' => 'online',
            'start_at' => now()->setHour(14)->setMinute(0)->setSecond(0),
            'end_at' => now()->setHour(16)->setMinute(0)->setSecond(0),
            'duration_minutes' => 120,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'created_by' => null,
        ]);

        // Future booking (pending)
        Booking::factory()->create([
            'booking_code' => 'KSC-BL-260720-FUT1',
            'billiard_table_id' => $tables[2]->id,
            'payment_method_id' => $pmBank->id,
            'customer_name' => 'Future Customer',
            'customer_phone' => '6283333333333',
            'booking_type' => 'online',
            'start_at' => now()->addDays(2)->setHour(19)->setMinute(0)->setSecond(0),
            'end_at' => now()->addDays(2)->setHour(20)->setMinute(0)->setSecond(0),
            'duration_minutes' => 60,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'created_by' => null,
        ]);
    }
}

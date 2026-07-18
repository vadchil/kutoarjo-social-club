<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('booking_code')->unique();
            $table->foreignUlid('billiard_table_id')->constrained('billiard_tables')->cascadeOnDelete();
            $table->foreignUlid('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('booking_type');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('duration_minutes');
            $table->integer('hourly_price');
            $table->integer('total_price');
            $table->string('status');
            $table->string('payment_status');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Composite indexes for query paths
            $table->index(['billiard_table_id', 'status', 'start_at', 'end_at'], 'bookings_table_avail_idx');
            $table->index(['customer_phone', 'booking_code'], 'bookings_phone_code_idx');
            $table->index(['start_at', 'end_at', 'status', 'payment_status', 'customer_phone', 'booking_code'], 'bookings_composite_reporting_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

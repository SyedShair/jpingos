<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Local, customer-facing reference (shown instead of the raw numeric id — same
            // convention the rest of this app already uses for order numbers etc).
            $table->string('booking_number')->unique();

            // Guestplan linkage — populated only after Guestplan confirms the reservation.
            $table->string('guestplan_booking_id')->nullable()->unique();
            $table->string('restaurant_id')->nullable();     // the GuestPlan restaurant id this was booked against
            $table->text('guestplan_error')->nullable();     // internal only — never returned by the API
            $table->timestamp('synced_at')->nullable();

            // Prevents a retried/duplicated request from creating a second Guestplan
            // reservation for what is really the same submission.
            $table->string('idempotency_key')->nullable()->unique();

            // Optional links to existing features (seasonal events, logged-in customer accounts).
            $table->foreignId('booking_event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            $table->date('booking_date');
            $table->time('booking_time');
            $table->unsignedInteger('party_size');
            $table->text('notes')->nullable();

            // pending: saved locally, Guestplan call not yet finished (or GuestPlan was down)
            // confirmed: Guestplan accepted it
            // failed: Guestplan rejected it (see guestplan_error) — never set to confirmed in this case
            // cancelled: cancelled by staff (DB only — see note in GuestplanService)
            $table->enum('status', ['pending', 'confirmed', 'failed', 'cancelled'])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_date', 'booking_time']);
            $table->index('status');
            $table->index('customer_email');
            $table->index('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

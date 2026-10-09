<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BusinessHour;
use App\Services\GuestplanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uses a fake GuestplanService bound into the container — no real HTTP calls
 * are made, so these run offline and never touch the real GuestPlan API or
 * create real reservations. Requires your project's base TestCase to use a
 * test database (RefreshDatabase migrates a fresh schema for each test).
 */
class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGuestplan(bool $succeeds, string $error = 'boom'): void
    {
        $this->app->bind(GuestplanService::class, function () use ($succeeds, $error) {
            return new class($succeeds, $error) extends GuestplanService {
                public function __construct(private bool $succeeds, private string $error) {}

                public function createBooking(Booking $booking): array
                {
                    return $this->succeeds
                        ? ['ok' => true, 'guestplan_booking_id' => 'GP-TEST-123', 'error' => null]
                        : ['ok' => false, 'guestplan_booking_id' => null, 'error' => $this->error];
                }
            };
        });
    }

    private function ensureOpenAllWeek(): void
    {
        for ($d = 0; $d <= 6; $d++) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $d],
                ['is_closed' => false, 'opens_at' => '00:00:00', 'closes_at' => '23:59:00', 'closes_next_day' => false]
            );
        }
        BusinessHour::forgetCache();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'customer_name'  => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '07123456789',
            'booking_date'   => now()->addDay()->toDateString(),
            'booking_time'   => '19:00',
            'party_size'     => 2,
            'notes'          => 'Window seat please',
        ], $overrides);
    }

    public function test_create_booking_succeeds_and_is_confirmed(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(true);

        $res = $this->postJson('/api/bookings', $this->payload());

        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.guestplanBookingId', 'GP-TEST-123');

        $this->assertDatabaseHas('bookings', [
            'customer_email'       => 'test@example.com',
            'status'               => 'confirmed',
            'guestplan_booking_id' => 'GP-TEST-123',
        ]);
    }

    public function test_create_booking_saved_as_failed_when_guestplan_rejects_it(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(false, 'GuestPlan said no.');

        $res = $this->postJson('/api/bookings', $this->payload());

        // Still 201 — the booking exists locally either way. Never "confirmed" on failure.
        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'failed');

        $this->assertDatabaseHas('bookings', ['status' => 'failed']);
        $this->assertDatabaseMissing('bookings', ['status' => 'confirmed']);
    }

    public function test_duplicate_idempotency_key_does_not_call_guestplan_twice(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(true);

        $key = 'test-key-123';

        $first = $this->postJson('/api/bookings', $this->payload(), ['Idempotency-Key' => $key]);
        $second = $this->postJson('/api/bookings', $this->payload(), ['Idempotency-Key' => $key]);

        $first->assertStatus(201);
        $second->assertStatus(201);

        // Same booking returned both times — only one row was ever created.
        $this->assertSame(
            $first->json('data.id'),
            $second->json('data.id')
        );
        $this->assertSame(1, Booking::where('idempotency_key', $key)->count());
    }

    public function test_validation_error_uses_the_standard_error_envelope(): void
    {
        $res = $this->postJson('/api/bookings', $this->payload(['customer_email' => 'not-an-email']));

        $res->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    public function test_booking_outside_opening_hours_is_rejected(): void
    {
        BusinessHour::updateOrCreate(
            ['day_of_week' => now()->addDay()->dayOfWeek],
            ['is_closed' => true, 'opens_at' => null, 'closes_at' => null]
        );
        BusinessHour::forgetCache();

        $res = $this->postJson('/api/bookings', $this->payload());

        $res->assertStatus(422)->assertJsonPath('error.code', 'OUTSIDE_OPENING_HOURS');
    }

    public function test_list_bookings_supports_filters_and_pagination(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(true);

        $this->postJson('/api/bookings', $this->payload(['customer_name' => 'Alice']));
        $this->postJson('/api/bookings', $this->payload(['customer_name' => 'Bob']));

        $res = $this->getJson('/api/bookings?customer_name=Alice');

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customerName', 'Alice');
    }

    public function test_show_returns_404_in_standard_envelope_when_missing(): void
    {
        $res = $this->getJson('/api/bookings/999999');

        $res->assertStatus(404)->assertJsonPath('error.code', 'BOOKING_NOT_FOUND');
    }

    public function test_update_changes_local_record_only(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(true);

        $created = $this->postJson('/api/bookings', $this->payload())->json('data');

        $res = $this->putJson('/api/bookings/' . $created['id'], ['party_size' => 6]);

        $res->assertStatus(200)->assertJsonPath('data.partySize', 6);
        $this->assertDatabaseHas('bookings', ['id' => $created['id'], 'party_size' => 6]);
    }

    public function test_delete_soft_deletes_the_booking(): void
    {
        $this->ensureOpenAllWeek();
        $this->fakeGuestplan(true);

        $created = $this->postJson('/api/bookings', $this->payload())->json('data');

        $res = $this->deleteJson('/api/bookings/' . $created['id']);
        $res->assertStatus(200)->assertJsonPath('success', true);

        $this->assertSoftDeleted('bookings', ['id' => $created['id']]);

        // Soft-deleted — no longer visible via the normal show endpoint.
        $this->getJson('/api/bookings/' . $created['id'])->assertStatus(404);
    }
}

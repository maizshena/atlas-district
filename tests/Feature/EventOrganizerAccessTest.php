<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Artist;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Venue;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// tests/Feature/EventOrganizerAccessTest.php uji hak akses dan alur review event organizer
class EventOrganizerAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_organizer_submission_is_pending_owned_and_hidden_from_public(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $venue = Venue::factory()->create();

        $this->actingAs($organizer)->post(route('admin.events.store'), [
            'venue_id' => $venue->id,
            'name' => 'Konser Uji',
            'slug' => 'konser-uji',
            'description' => 'Deskripsi konser uji',
            'start_date' => now()->addMonth()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->addHours(4)->format('Y-m-d H:i:s'),
            'artist_ids' => [$this->artist()->id],
            'is_closed' => 1,
        ])->assertRedirect(route('admin.events.index'));

        $event = Event::query()->where('slug', 'konser-uji')->firstOrFail();

        $this->assertSame(EventStatus::PendingReview, $event->status);
        $this->assertSame($organizer->id, $event->organizer_id);
        $this->assertFalse($event->is_closed);

        auth()->logout();
        $this->get(route('events.show', 'konser-uji'))->assertNotFound();
    }

    public function test_organizer_must_choose_at_least_one_artist(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();

        $this->actingAs($organizer)->post(route('admin.events.store'), [
            'venue_id' => Venue::factory()->create()->id,
            'name' => 'Tanpa Artis',
            'slug' => 'tanpa-artis',
            'description' => 'Deskripsi',
            'start_date' => now()->addMonth()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->addHours(4)->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors('artist_ids');
    }

    public function test_organizer_update_only_changes_info_and_ignores_locked_fields(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $originalVenueId = $event->venue_id;
        $originalStart = $event->start_date;

        $this->actingAs($organizer)->put(route('admin.events.update', $event), [
            'name' => 'Nama Baru',
            'description' => 'Deskripsi baru',
            'venue_id' => Venue::factory()->create()->id,
            'start_date' => now()->addYear()->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $fresh = $event->fresh();

        $this->assertSame('Nama Baru', $fresh->name);
        $this->assertSame($originalVenueId, $fresh->venue_id);
        $this->assertTrue($fresh->start_date->equalTo($originalStart));
    }

    public function test_organizer_cannot_touch_another_organizers_event(): void
    {
        $owner = User::factory()->eventOrganizer()->create();
        $other = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $owner->id]);

        $this->actingAs($other)->get(route('admin.events.edit', $event))->assertForbidden();
        $this->actingAs($other)->put(route('admin.events.update', $event), ['name' => 'Bajakan'])->assertForbidden();
        $this->actingAs($other)->get(route('admin.events.show', $event))->assertForbidden();
    }

    public function test_organizer_and_customer_cannot_reach_admin_only_areas(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $customer = User::factory()->create();

        foreach ([route('admin.dashboard'), route('admin.venues.index'), route('admin.orders.index'), route('admin.gate')] as $url) {
            $this->actingAs($organizer)->get($url)->assertForbidden();
        }

        $this->actingAs($customer)->get(route('admin.events.index'))->assertForbidden();
    }

    public function test_admin_publish_requires_a_ticket_category(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->pendingFor($organizer)->create();

        $this->actingAs($admin)->patch(route('admin.events.publish', $event))->assertSessionHas('error');
        $this->assertSame(EventStatus::PendingReview, $event->fresh()->status);

        TicketCategory::factory()->create(['event_id' => $event->id]);

        $this->actingAs($admin)->patch(route('admin.events.publish', $event))->assertSessionHas('status');
        $this->assertSame(EventStatus::Published, $event->fresh()->status);
    }

    public function test_pending_event_cannot_be_ordered(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->pendingFor($organizer)->create();
        $category = TicketCategory::factory()->create(['event_id' => $event->id, 'quota' => 10, 'sold' => 0]);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->create(User::factory()->create(), [['ticket_category_id' => $category->id, 'quantity' => 1]]);
    }

    private function artist(): Artist
    {
        return Artist::query()->create(['name' => 'Band Uji', 'slug' => 'band-uji-'.Str::lower(Str::random(5))]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventChangeLog;
use App\Models\EventNotice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// tests/Feature/EventNoticeAndAuditTest.php uji pengumuman otomatis dan manual serta riwayat perubahan event
class EventNoticeAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_organizer_edit_on_published_event_logs_change_and_shows_public_notice(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => EventStatus::Published]);

        $this->actingAs($organizer)->put(route('admin.events.update', $event), [
            'name' => 'Nama Baru',
            'description' => 'Deskripsi yang sudah diperbarui',
        ])->assertRedirect();

        $log = EventChangeLog::query()->where('event_id', $event->id)->firstOrFail();
        $this->assertSame('Nama Baru', $log->changes['name']['to']);
        $this->assertSame($organizer->id, $log->user_id);
        $this->assertTrue(EventNotice::query()->where('event_id', $event->id)->where('is_automatic', true)->exists());

        $this->app['auth']->forgetGuards();
        $this->get(route('events.show', $event->slug))->assertOk()->assertSee('Informasi event diperbarui');
    }

    public function test_pending_event_edit_is_logged_without_public_notice(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->pendingFor($organizer)->create();

        $this->actingAs($organizer)->put(route('admin.events.update', $event), ['name' => 'Revisi Draft'])->assertRedirect();

        $this->assertSame(1, EventChangeLog::query()->where('event_id', $event->id)->count());
        $this->assertSame(0, EventNotice::query()->where('event_id', $event->id)->count());
    }

    public function test_admin_schedule_change_keeps_a_single_automatic_schedule_notice(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();

        foreach ([2, 3] as $months) {
            $this->actingAs($admin)->put(route('admin.events.update', $event), [
                'start_date' => now()->addMonths($months)->format('Y-m-d H:i:s'),
                'end_date' => now()->addMonths($months)->addHours(4)->format('Y-m-d H:i:s'),
            ])->assertRedirect();
        }

        $notices = EventNotice::query()->where('event_id', $event->id)->where('is_automatic', true)->get();

        $this->assertCount(1, $notices);
        $this->assertStringContainsString('Jadwal', $notices->first()->message);
    }

    public function test_organizer_posts_and_dismisses_a_manual_notice(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => EventStatus::Published]);

        $this->actingAs($organizer)
            ->post(route('admin.events.notices.store', $event), ['message' => 'Pintu dibuka pukul 17.00', 'days' => 3])
            ->assertRedirect();

        $notice = EventNotice::query()->where('event_id', $event->id)->where('is_automatic', false)->firstOrFail();
        $this->assertTrue($notice->expires_at->isFuture());

        $this->actingAs($organizer)->delete(route('admin.event-notices.destroy', $notice))->assertRedirect();
        $this->assertTrue($notice->fresh()->expires_at->lessThanOrEqualTo(now()));

        $this->app['auth']->forgetGuards();
        $this->get(route('events.show', $event->slug))->assertOk()->assertDontSee('Pintu dibuka pukul 17.00');
    }

    public function test_notice_with_link_is_rejected(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => EventStatus::Published]);

        $this->actingAs($organizer)
            ->post(route('admin.events.notices.store', $event), ['message' => 'Beli di https://contoh.example', 'days' => 3])
            ->assertSessionHasErrors('message');

        $this->assertSame(0, EventNotice::query()->count());
    }

    public function test_notice_permissions_follow_ownership_and_publication(): void
    {
        $owner = User::factory()->eventOrganizer()->create();
        $other = User::factory()->eventOrganizer()->create();
        $published = Event::factory()->create(['organizer_id' => $owner->id, 'status' => EventStatus::Published]);
        $pending = Event::factory()->pendingFor($owner)->create();
        $payload = ['message' => 'Info penting', 'days' => 1];

        $this->actingAs($other)->post(route('admin.events.notices.store', $published), $payload)->assertForbidden();
        $this->actingAs($owner)->post(route('admin.events.notices.store', $pending), $payload)->assertForbidden();

        $notice = EventNotice::query()->create(['event_id' => $published->id, 'author_id' => $owner->id, 'message' => 'Lama', 'is_automatic' => false, 'expires_at' => now()->addDay()]);
        $this->actingAs($other)->delete(route('admin.event-notices.destroy', $notice))->assertForbidden();
    }
}

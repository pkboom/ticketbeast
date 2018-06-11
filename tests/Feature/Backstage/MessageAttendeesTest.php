<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendAttendeeMessage;

class MessageAttendeesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->concert = factory(Concert::class)->create();
    }

    /** @test */
    public function a_guest_cannot_create_a_message()
    {
        $this->get(route('backstage.concert-messages.new', 1))
            ->assertRedirect(route('login'));

        $this->post(route('backstage.concert-messages.store', 1))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function a_promoter_cannot_create_a_message_for_other_concerts()
    {
        $message = ['subject' => 'My subject', 'message' => 'My message'];

        $this->signIn()
            ->get(route('backstage.concert-messages.new', $this->concert))
            ->assertStatus(404);

        $this->post(route('backstage.concert-messages.store', $this->concert), $message)
            ->assertStatus(404);
    }

    /** @test */
    public function a_promoter_can_view_the_message_form_for_their_own_concert()
    {
        $this->signIn($this->concert->user)
            ->get(route('backstage.concert-messages.new', $this->concert))
            ->assertSee($this->concert->title);
    }

    /** @test */
    public function a_promoter_can_send_a_new_message()
    {
        $this->withoutExceptionHandling();
        Queue::fake();

        $message = ['subject' => 'My subject', 'message' => 'My message'];

        $this->signIn($this->concert->user)
            ->post(route('backstage.concert-messages.store', $this->concert), $message)
            ->assertSessionHas('flash');

        $this->assertDatabaseHas('attendee_messages', $message);

        Queue::assertPushed(SendAttendeeMessage::class, function ($job) use ($message) {
            // dd($job->attendMessage->toArray());
            // dd($message);

            return $job->attendMessage->is($message);
        });
    }

    /** @test */
    public function subject_is_required_to_send_a_message()
    {
        Queue::fake();

        $this->sendMessage(['subject' => ''])
            ->assertSessionHasErrors('subject');

        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /** @test */
    public function message_is_required_to_send_a_message()
    {
        Queue::fake();

        $this->sendMessage(['message' => ''])
            ->assertSessionHasErrors('message');

        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    public function sendMessage($overrides = [])
    {
        return $this->signIn($this->concert->user)
            ->post(route('backstage.concert-messages.store', $this->concert), $overrides);
    }
}

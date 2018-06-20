<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\SendAttendeeMessage;
use App\Factory\ConcertFactory;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendeeMessageEmail;
use App\Factory\OrderFactory;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();

        $concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $orderA = OrderFactory::createForConcert($concert, ['email' => 'a@a.com', ]);
        $orderB = OrderFactory::createForConcert($concert, ['email' => 'b@b.com', ]);

        $message = $concert->attendeeMessages()->create([
            'subject' => 'new subject',
            'message' => 'new message',
        ]);

        SendAttendeeMessage::dispatch($message);

        // Mail::assertSent(AttendeeMessageEmail::class, function ($mail) use ($orderA) {
        //     return $mail->hasTo($orderA->email);
        // });

        // Mail::assertSent(AttendeeMessageEmail::class, function ($mail) use ($orderB) {
        //     return $mail->hasTo($orderB->email);
        // });

        // Mail::assertSent(AttendeeMessageEmail::class, 2);

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($orderA) {
            return $mail->hasTo($orderA->email);
        });

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($orderB) {
            return $mail->hasTo($orderB->email);
        });

        Mail::assertQueued(AttendeeMessageEmail::class, 2);
    }
}

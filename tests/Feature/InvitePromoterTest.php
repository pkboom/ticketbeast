<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Facades\InvitationCode;
use App\Invitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEmail;

class InvitePromoterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_invites_a_promoter_via_the_cli()
    {
        Mail::fake();

        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $this->artisan('invite-promoter', ['email' => 'john@example.com']);

        $invitation = Invitation::first();

        $this->assertEquals(1, Invitation::count());

        $this->assertDatabaseHas('invitations', ['email' => 'john@example.com']);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo($invitation->email) &&
                    $mail->invitation->is($invitation);
        });
    }
}

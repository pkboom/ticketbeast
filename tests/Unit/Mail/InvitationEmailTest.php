<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Invitation;
use App\Mail\InvitationEmail;

class InvitationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_contains_a_link_to_accept_the_invitation()
    {
        $invitation = factory(Invitation::class)->create();

        $email = new InvitationEmail($invitation);

        $this->assertContains(url("/invitations/{$invitation->code}"), $email->render());
    }

    /** @test */
    public function email_has_a_correct_subject()
    {
        $invitation = factory(Invitation::class)->create();

        $email = new InvitationEmail($invitation);

        $this->assertContains("You're invited to join TicketBeast", $email->build()->subject);
    }
}

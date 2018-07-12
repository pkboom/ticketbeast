<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Invitation;
use App\User;
use Illuminate\Support\Facades\Hash;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->invitation = factory(Invitation::class)->create(['code' => 'TESTCODE1234']);
    }

    /** @test */
    public function viewing_an_unused_invitation()
    {
        $response = $this->get(route('invitations.show', 'TESTCODE1234'));

        $response->assertViewIs('invitation.show');

        $this->assertTrue($response->data('invitation')->is($this->invitation));
    }

    /** @test */
    public function it_may_not_view_a_used_invitation()
    {
        $this->addUserToInvitation(['user_id' => 1]);

        $this->get(route('invitations.show', 'TESTCODE1234'))
            ->assertStatus(404);
    }

    /** @test */
    public function it_may_not_view_an_invalid_invitation()
    {
        $this->get(route('invitations.show', 'TESTCODE1111'))
            ->assertStatus(404);
    }

    /** @test */
    public function registering_with_a_valid_invitation_code()
    {
        $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ])->assertRedirect(route('backstage.concerts.index'));

        $user = User::first();

        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));

        $this->assertTrue($this->invitation->fresh()->user->is($user));
    }

    /** @test */
    public function it_does_not_register_with_an_invalid_invitation_code()
    {
        $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1111'
        ])->assertStatus(404);
    }

    /** @test */
    public function it_requires_some_data()
    {
        $this->post('/register')
            ->assertSessionHasErrors('email')
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function email_must_be_an_email()
    {
        $this->post('/register', ['email' => 'not email'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function email_must_be__unique()
    {
        $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234'
        ]);

        $this->assertEquals(1, User::all()->count());

        auth()->logout();

        $anotherInvitation = factory(Invitation::class)->create(['code' => 'TESTCODE1111']);

        $this->post('/register', ['email' => 'john@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function addUserToInvitation($override)
    {
        $this->invitation->update($override);
    }
}

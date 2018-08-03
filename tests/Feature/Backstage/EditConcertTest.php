<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use App\User;

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->concert = factory(Concert::class)->create();
    }

    /** @test */
    public function guest_may_not_edit_a_concert()
    {
        $this->get(route('backstage.concerts.edit', 1))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function promoters_can_not_edit_their_published_concerts()
    {
        $this->withoutExceptionHandling();

        $this->concert->publish();

        $this->expectException(\Exception::class);

        $this->signIn();

        $this->get(route('backstage.concerts.edit', $this->concert));
    }

    /** @test */
    public function promoters_cannot_edit_other_concerts()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->be(factory(User::class)->create())
            ->get(route('backstage.concerts.edit', $this->concert))
            ->assertDontSee($this->concert->title)
            ->assertDontSee($this->concert->ticket_price);
    }

    /** @test */
    public function promoters_can_see_their_unpublished_concerts()
    {
        $this->signIn($this->concert->user);

        $this->get(route('backstage.concerts.edit', $this->concert))
            ->assertSee($this->concert->title)
            ->assertSee($this->concert->venue);
    }

    /** @test */
    public function guest_may_not_update_of_any_concert()
    {
        $this->patch(route('backstage.concerts.update', 1))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function promoters_can_not_update_their_published_concerts()
    {
        $this->withoutExceptionHandling();

        $this->concert->publish();

        $this->expectException(\Exception::class);

        $this->be($this->concert->user)
            ->patch(route('backstage.concerts.update', $this->concert));
    }

    /** @test */
    public function promoters_can_not_update_other_concerts()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->be(factory(User::class)->create())
            ->patch(route('backstage.concerts.update', $this->concert));
    }

    /** @test */
    public function promoters_can_edit_their_own_unpublished_concerts()
    {
        $this->be($this->concert->user)
            ->patch(
                route('backstage.concerts.update', $this->concert),
                $updatedConcert = [
                    'title' => 'new title',
                    'venue' => 'new venue',
                ]
            );

        $this->get(route('backstage.concerts.edit', $this->concert))
            ->assertSee($updatedConcert['title'])
            ->assertSee($updatedConcert['venue']);
    }

    /** @test */
    public function concert_needs_these_info()
    {
        $this->publishConcert()
            ->assertSessionHasErrors('title')
            ->assertSessionHasErrors('date')
            ->assertSessionHasErrors('time')
            ->assertSessionHasErrors('venue')
            ->assertSessionHasErrors('venue_address')
            ->assertSessionHasErrors('city')
            ->assertSessionHasErrors('state')
            ->assertSessionHasErrors('zip')
            ->assertSessionHasErrors('ticket_price')
            ->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    public function date_must_be_valid()
    {
        $this->publishConcert(['date' => 'invalid data'])
            ->assertSessionHasErrors('date');
    }

    /** @test */
    public function time_must_be_valid()
    {
        $this->publishConcert(['time' => 'invalid time'])
            ->assertSessionHasErrors('time');
    }

    /** @test */
    public function ticket_price_must_be_integer()
    {
        $this->publishConcert(['ticket_price' => 'invalid ticket_price'])
            ->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    public function ticket_price_must_be_at_least_5()
    {
        $this->publishConcert(['ticket_price' => 4])
            ->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    public function ticket_quantity_must_be_numeric()
    {
        $this->publishConcert(['ticket_quantity' => 'invalid ticket_quantity'])
            ->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1()
    {
        $this->publishConcert(['ticket_quantity' => 0])
            ->assertSessionHasErrors('ticket_quantity');
    }

    public function publishConcert($options = [])
    {
        $user = isset($options['user_id']) ?
            User::find($options['user_id']) :
            factory(User::class)->create();

        $this->be($user);

        return $this->post('/backstage/concerts', $options);
    }
}

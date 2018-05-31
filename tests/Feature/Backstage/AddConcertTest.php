<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Concert;
use Carbon\Carbon;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_may_not_add_a_concert()
    {
        $this->post('/backstage/concerts')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function promoters_can_add_a_concert()
    {
        $concert = factory(Concert::class)->make()->toArray();

        $concert = $this->publishConcertWithUser($concert)->json();

        // $this->assertEquals($concert->ticket_quantity, Concert::find(1)->tickets->count());

        $this->assertDatabaseHas('concerts', array_merge($concert, [
            'date' => Carbon::parse(vsprintf('%s %s', ['2017-11-10', '8:00pm'])),
        ]));
    }

    /** @test */
    public function additional_information_is_optional()
    {
        $concert = factory(Concert::class)->make()->toArray();

        unset($concert['additional_information']);

        $concert = $this->publishConcertWithUser($concert)->json();

        $this->assertDatabaseHas('concerts', array_merge($concert, [
            'date' => Carbon::parse(vsprintf('%s %s', ['2017-11-10', '8:00pm']))
        ]));
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $concert = factory(Concert::class)->make()->toArray();

        unset($concert['subtitle']);

        $concert = $this->publishConcertWithUser($concert)->json();

        $this->assertDatabaseHas('concerts', array_merge($concert, [
            'date' => Carbon::parse(vsprintf('%s %s', ['2017-11-10', '8:00pm']))
        ]));
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
    public function ticket_price_must_be_numeric()
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

    public function publishConcertWithUser($options)
    {
        $concertWithTicketAndTime = array_merge($options, [
            // 'ticket_quantity' => '2',
            'date' => '2017-11-10',
            'time' => '8:00pm'
        ]);

        $this->be(User::find($options['user_id']));

        return $this->postJson('/backstage/concerts', $concertWithTicketAndTime);
    }
}

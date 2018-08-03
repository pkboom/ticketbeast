<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Events\ConcertAdded;
use Illuminate\Support\Facades\Event;

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

        $concert = $this->publishConcertWithJson($concert)->json();

        $this->assertDatabaseHas('concerts', array_merge($concert, [
            'date' => Carbon::parse(vsprintf('%s %s', ['2017-11-10', '8:00pm'])),
        ]));
    }

    /** @test */
    public function additional_information_is_optional()
    {
        $concert = factory(Concert::class)->make()->toArray();

        unset($concert['additional_information']);

        $concert = $this->publishConcertWithJson($concert)->json();

        $this->assertDatabaseHas('concerts', array_merge($concert, [
            'date' => Carbon::parse(vsprintf('%s %s', ['2017-11-10', '8:00pm']))
        ]));
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $concert = factory(Concert::class)->make()->toArray();

        unset($concert['subtitle']);

        $concert = $this->publishConcertWithJson($concert)->json();

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
        $this->signIn();

        return $this->post('/backstage/concerts', $options);
    }

    public function publishConcertWithJson($options)
    {
        $this->signIn();

        $options = $this->addDateAndTime($options);

        // return $this->getUser($options)->postJson(route('backstage.concerts.store'), $options);
        return $this->postJson(route('backstage.concerts.store'), $options);
    }

    public function addDateAndTime($options)
    {
        return array_merge($options, [
            'date' => '2017-11-10',
            'time' => '8:00pm',
        ]);
    }

    /** @test */
    public function poster_image_is_uploaded_if_included()
    {
        $this->withoutExceptionHandling();
        $width = 600;
        $height = $width * 11 / 8.5;

        Event::fake([ConcertAdded::class]);

        Storage::fake('public');

        $options = factory(Concert::class)->make()->toArray();

        $concert = $this->publishConcertWithJson($options + [
            'poster_image' => $file = UploadedFile::fake()->image('concert-poster.jpg', $width, $height)
        ]);

        $this->assertEquals('posters/' . $file->hashName(), Concert::first()->poster_image_path);

        Storage::disk('public')->assertExists('posters/' . $file->hashName());
    }

    /** @test */
    public function poster_image_must_be_an_image()
    {
        $this->publishConcert(['poster_image' => 'non-image'])
            ->assertSessionHasErrors('poster_image');
    }

    /** @test */
    public function post_image_must_be_at_least_600px_wide()
    {
        $width = 599;
        $height = $width * 11 / 8.5;

        $this->publishConcert(['poster_image' => UploadedFile::fake()->image('concert-poster.jpg', $width, $height)])
            ->assertSessionHasErrors('poster_image');
    }

    /** @test */
    public function poster_image_must_have_a_letter_aspect_ratio()
    {
        $width = 600;
        $height = $width * 12 / 8.5;

        $this->publishConcert(['poster_image' => UploadedFile::fake()->image('concert-poster.jpg', $width, $height)])
            ->assertSessionHasErrors('poster_image');

        $height = $width * 10 / 8.5;

        $this->publishConcert(['poster_image' => UploadedFile::fake()->image('concert-poster.jpg', $width, $height)])
            ->assertSessionHasErrors('poster_image');

        $height = $width * 11 / 8.6;

        $this->publishConcert(['poster_image' => UploadedFile::fake()->image('concert-poster.jpg', $width, $height)])
            ->assertSessionHasErrors('poster_image');

        $height = $width * 11 / 8.4;

        $this->publishConcert(['poster_image' => UploadedFile::fake()->image('concert-poster.jpg', $width, $height)])
            ->assertSessionHasErrors('poster_image');
    }

    /** @test */
    public function an_event_is_fired_when_a_concert_is_added()
    {
        Event::fake();

        $concert = factory(Concert::class)->make()->toArray();

        $concert = $this->publishConcertWithJson($concert)->json();

        Event::assertDispatched(ConcertAdded::class, function ($e) use ($concert) {
            return $e->concert->id === $concert['id'];
        });
    }
}

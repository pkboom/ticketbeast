<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->create([
            'published_at' => today()
        ]);

        $this->get('/concerts/' . $concert->id)
            ->assertSee($concert->title)
            ->assertSee($concert->venue)
            ->assertSee($concert->addtional_information);
    }

    /** @test */
    public function user_cannot_view_unpublished_concert_listings()
    {
        $this->withoutExceptionHandling();

        $concert = create(Concert::class);

        $this->expectException(\Exception::class);

        $this->get(route('concerts.show', $concert));
    }
}

<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Factory\ConcertFactory;

class ViewPublishedConcertOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_may_not_view_orders_of_published_concerts()
    {
        $this->get(route('backstage.published-concert-orders.index', 1))
            ->assertRedirect('/login');
    }

    /** @test */
    public function a_promoter_can_view_the_orders_of_their_own_published_conerts()
    {
        $concert = ConcertFactory::createPublished();
        $unpublishedConcert = ConcertFactory::createUnpublished();

        $this->signIn($concert->user);

        $this->get(route('backstage.published-concert-orders.index', $concert->id))
            ->assertSee($concert->title)
            ->assertDontSee($unpublishedConcert->title);
    }
}

<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Database\Eloquent\Collection;
use App\Factory\ConcertFactory;
use PHPUnit\Framework\Assert;

class ViewConcertListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contains the specified value.');
        });

        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), 'Failed asserting that the collection does not contain the specified value.');
        });

        Collection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items)); //$items = [a, c]

            $this->zip($items)->each(function ($pair) {
                [$a, $b] = $pair;

                Assert::assertTrue($a->is($b));
            });
        });
    }

    /** @test */
    public function guests_may_not_view_a_promoters_concert_list()
    {
        $this->get('/backstage/concerts')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function promoters_can_view_a_list_of_their_concerts()
    {
        $user = factory(User::class)->create();

        $concerts = factory(Concert::class, 3)->create(['user_id' => $user->id]);

        $this->be($user)
            ->get('/backstage/concerts')
            ->assertSee($concerts[0]->title)
            ->assertSee($concerts[0]->venue)
            ->assertSee($concerts[1]->title)
            ->assertSee($concerts[1]->venue)
            ->assertSee($concerts[2]->title)
            ->assertSee($concerts[2]->venue);
    }

    /** @test */
    public function t()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $publishedConcertA = ConcertFactory::createPublished(['user_id' => $user->id]);
        $publishedConcertB = ConcertFactory::createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = ConcertFactory::createPublished(['user_id' => $user->id]);
        $unpublishedConcertA = ConcertFactory::createUnpublished(['user_id' => $user->id]);
        $unpublishedConcertB = ConcertFactory::createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = ConcertFactory::createUnpublished(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->data('publishedConcerts')->assertEquals([
            $publishedConcertA,
            $publishedConcertC,
        ]);
        $response->data('unpublishedConcerts')->assertEquals([
            $unpublishedConcertA,
            $unpublishedConcertC,
        ]);
    }
}

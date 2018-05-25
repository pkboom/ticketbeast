<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Database\Eloquent\Collection;

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
}

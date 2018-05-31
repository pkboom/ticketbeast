<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_may_not_publish_concerts()
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Exception::class);

        $this->post('/backstage/published-concerts', ['concert_id' => 1]);
    }

    /** @test */
    public function a_promoter_cannot_publish_other_concerts()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->create();

        $this->expectException(\Exception::class);

        $this->signIn()
            ->post('/backstage/published-concerts', ['concert_id' => $concert->id]);
    }

    /** @test */
    public function a_promoter_can_publish_their_own_concert()
    {
        $concert = factory(Concert::class)->create();

        $this->signIn($concert->user)
            ->post('/backstage/published-concerts', ['concert_id' => $concert->id])
            ->assertRedirect(route('backstage.concerts.index'));

        $this->assertTrue($concert->fresh()->isPublished());
    }
}

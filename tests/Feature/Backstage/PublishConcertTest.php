<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_promoter_can_publish_their_own_concert()
    {
        // Given a promoter, unpublished concert
        $concert = factory(Concert::class)->create();

        // If they published it

        // Then it is published
    }
}

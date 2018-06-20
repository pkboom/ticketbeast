<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\ProcessPosterImage;
use Illuminate\Support\Facades\Queue;
use App\Factory\ConcertFactory;
use App\Events\ConcertAdded;

class SchedulePosterImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_queues_a_job_to_process_a_poster_image_if_a_poster_image_is_present()
    {
        Queue::fake();

        $concert = ConcertFactory::createPublished(['poster_image_path' => 'poster-image.png']);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->id === $concert->id;
        });
    }

    /** @test */
    public function a_job_is_not_queued_if_a_poster_is_not_present()
    {
        Queue::fake();

        $concert = ConcertFactory::createPublished();

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
    }
}

<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Factory\ConcertFactory;
use App\Events\ConcertAdded;

class ProcessPostImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake('public');

        $width = 800;
        $height = $width * 11 / 8.5;
        $poster_image_path = 'posters/concert-poster.jpg';

        Storage::disk('public')->putFileAs('posters', UploadedFile::fake()->image('concert-poster.jpg', $width, $height), 'concert-poster.jpg');

        $concert = ConcertFactory::createPublished([
            'poster_image_path' => $poster_image_path
        ]);

        ConcertAdded::dispatch($concert);

        $image = Storage::disk('public')->get($poster_image_path);

        // $data = getimagesizefromstring($image);
        // list($width) = getimagesizefromstring($image);
        [$savedImageWidth, $savedImageHeight] = getimagesizefromstring($image);

        $this->assertEquals(600, $savedImageWidth);
        $this->assertEquals(776, $savedImageHeight);
    }
}

<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertController extends Controller
{
    public function show(Concert $concert)
    {
        abort_unless($concert->isPublished(), 404);

        return view('concerts.show', compact('concert'));
    }
}

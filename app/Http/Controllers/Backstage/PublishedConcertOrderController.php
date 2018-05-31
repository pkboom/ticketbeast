<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;

class PublishedConcertOrderController extends Controller
{
    public function index($concertId)
    {
        $concert = auth()->user()->concerts()->published()->findOrFail($concertId);

        return view('backstage.published-concert-orders.index', [
            'concert' => $concert,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Concert;

class ConcertController extends Controller
{
    public function index()
    {
        return view('backstage.concerts.index', [
            'publishedConcerts' => auth()->user()->concerts->filter->isPublished(),
            'unpublishedConcerts' => auth()->user()->concerts->reject->isPublished(),
        ]);
    }

    public function create()
    {
        return view('backstage.concerts.create');
    }

    public function store()
    {
        request()->validate([
            'title' => 'required',
            'date' => 'required | date',
            'time' => 'required | date_format:g:ia',
            'venue' => 'required',
            'venue_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'ticket_price' => 'required | numeric | min:5',
            'ticket_quantity' => 'required | numeric | min:1',
        ]);

        $result = request()->except(['date', 'time']);

        $date = Carbon::parse(vsprintf('%s %s', [request('date'), request('time')]));

        $result = $result + [
            'date' => $date
        ];

        // $concert = tap(auth()->user()->concerts()->create($result))->publish();
        $concert = auth()->user()->concerts()->create($result);

        if (request()->wantsJson()) {
            return response($concert, 201);
        }

        return redirect()->route('backstage.concerts.index');
    }

    public function edit(Concert $concert)
    {
        abort_unless(auth()->user()->id === $concert->user->id, 403);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', compact('concert'));
    }

    public function update(Concert $concert)
    {
        request()->validate([
            'title' => 'required',
            'date' => 'required | date',
            'time' => 'required | date_format:g:ia',
            'venue' => 'required',
            'venue_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'ticket_price' => 'required | numeric | min:5',
            'ticket_quantity' => 'required | numeric | min:1',
        ]);

        abort_if($concert->isPublished(), 403);

        $result = request()->except(['date', 'time']);

        $date = Carbon::parse(vsprintf('%s %s', [request('date'), request('time')]));

        $result = $result + [
            'date' => $date
        ];

        $concert->update($result);

        return redirect()->route('backstage.concerts.index');
    }
}

<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Concert;
use App\Events\ConcertAdded;

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
            'poster_image' => 'nullable | image | dimensions:min_width=600,ratio=8.5/11'
        ]);

        $result = request()->except(['date', 'time', 'poster_image']);

        $date = Carbon::parse(vsprintf('%s %s', [request('date'), request('time')]));

        $result = array_merge($result, [
            'date' => $date,
            'poster_image_path' => optional(request('poster_image'))->store('posters', 'public'),
        ]);

        $concert = auth()->user()->concerts()->create($result);

        ConcertAdded::dispatch($concert);

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

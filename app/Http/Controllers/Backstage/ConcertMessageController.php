<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Jobs\SendAttendeeMessage;

class ConcertMessageController extends Controller
{
    public function create($concertId)
    {
        $concert = auth()->user()->concerts()->findOrFail($concertId);

        return view('backstage.concert-messages.new', compact('concert'));
    }

    public function store($concertId)
    {
        request()->validate([
            'subject' => 'required',
            'message' => 'required'
        ]);

        $concert = auth()->user()->concerts()->findOrFail($concertId);

        $message = $concert->attendeeMessages()->create(request(['subject', 'message']));

        SendAttendeeMessage::dispatch($message);

        return redirect()->route('backstage.concert-messages.new', $concert)
            ->with('flash', 'Your message has been sent.');
    }
}

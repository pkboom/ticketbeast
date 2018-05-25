@extends('layouts.master')

@section('body')
    <header>
        <nav>
            <div>
                <div>
                    <img src="/img/logo.svg" alt="" style="height: 2.5rem;">
                </div>
                <div>
                    <form action="/logout" method="POST" class="inline-block">
                        @csrf
                        <button type="submit">Log out</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="bg-grey-light border-b">
        <div class="container">
            <h1 class="text-lg">Add a concert</h1>
        </div>
    </div>

    <form action="/backstage/concerts" method="post">
        @csrf

        @if ($errors->any())
            <div class="container">
                <h2 class="font-bold">
                    There {{ $errors->count() == 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ str_plural('error', $errors->count()) }} with this concert:
                </h2>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="border-b">
            <div class="container">
                <div>
                    <div>
                        <div>
                            <h2>Concert Details</h2>
                            <p class="text-grey-dark text-sm">Tell us who's playing! (Please be Slayer!)</p>
                            <p class="text-grey-dark text-sm">
                                Include the headliner in the concert name, use the subtitle section
                                to list any opening bands, and add any important information to the description.
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="card">
                            <div>
                                <div class="form-group {{ $errors->first('title', 'has-error') }}">
                                    <label class="form-label">Title</label>
                                    <input name="title" class="form-control" value="{{ old('title') }}" placeholder="The Headliners">
                                </div>
                                <div class="form-group {{ $errors->first('subtitle', 'has-error') }}">
                                    <label class="form-label">Subtitle</label>
                                    <input name="subtitle" class="form-control" value="{{ old('subtitle') }}" placeholder="with The Openers (optional)">
                                </div>
                                <div class="form-group {{ $errors->first('additional_information', 'has-error') }}">
                                    <label class="form-label">Additional Information</label>
                                    <textarea name="additional_information" class="form-control" rows="4" placeholder="This concert is 19+ (optional)">{{ old('additional_information') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-b">
            <div class="container">
                <div>
                    <div>
                        <div>
                            <h2>Date &amp; Time</h2>
                            <p class="text-grey-dark text-sm">
                                True metalheads really only care about the obscure openers,
                                so make sure they don't get there late!
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="card">
                            <div>
                                <div>
                                    <div class="col col-md-6">
                                        <div class="form-group {{ $errors->first('date', 'has-error') }}">
                                            <label class="form-label">Date</label>
                                            <input type="date" name="date" class="form-control" placeholder="yyyy-mm-dd" value="{{ old('date') }}">
                                        </div>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="form-group {{ $errors->first('time', 'has-error') }}">
                                            <label class="form-label">Start Time</label>
                                            <input  name="time" class="form-control" placeholder="7:00pm" value="{{ old('time') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-b p-xs-b-4 m-xs-b-4">
            <div class="container">
                <div class="row">
                    <div class="col col-lg-4">
                        <div class="p-xs-y-4">
                            <h2 class="text-base wt-medium m-xs-b-4">Venue Information</h2>
                            <p class="text-dark-soft text-sm">Where's the show? Let attendees know the venue name and address so they can bring the mosh.</p>
                        </div>
                    </div>
                    <div class="col col-lg-8">
                        <div class="card">
                            <div class="card-section">
                                <div class="form-group {{ $errors->first('venue', 'has-error') }}">
                                    <label class="form-label">Venue Name</label>
                                    <input name="venue" class="form-control" value="{{ old('venue') }}" placeholder="The Mosh Pit">
                                </div>
                                <div class="form-group {{ $errors->first('venue_address', 'has-error') }}">
                                    <label class="form-label">Stree Address</label>
                                    <input name="venue_address" class="form-control" value="{{ old('venue_address') }}" placeholder="500 Example Ave.">
                                </div>
                                <div class="row">
                                    <div class="col col-sm-4">
                                        <div class="form-group {{ $errors->first('city', 'has-error') }}">
                                            <label class="form-label">City</label>
                                            <input name="city" class="form-control" value="{{ old('city') }}" placeholder="Laraville">
                                        </div>
                                    </div>
                                    <div class="col col-sm-4">
                                        <div class="form-group {{ $errors->first('state', 'has-error') }}">
                                            <label class="form-label">State/Province</label>
                                            <input name="state" class="form-control" value="{{ old('state') }}" placeholder="ON">
                                        </div>
                                    </div>
                                    <div class="col col-sm-4">
                                        <div class="form-group {{ $errors->first('zip', 'has-error') }}">
                                            <label class="form-label">ZIP</label>
                                            <input name="zip" class="form-control" value="{{ old('zip') }}" placeholder="90210">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-b p-xs-b-4 m-xs-b-4">
            <div class="container">
                <div class="row">
                    <div class="col col-lg-4">
                        <div class="p-xs-y-4">
                            <h2 class="text-base wt-medium m-xs-b-4">Tickets &amp; Pricing</h2>
                            <p class="text-dark-soft text-sm">Set your ticket price and availability, but don't forget, metalheads are cheap so keep it reasonable.</p>
                        </div>
                    </div>
                    <div class="col col-lg-8">
                        <div class="card">
                            <div class="card-section">
                                <div class="row">
                                    <div class="col col-md-6">
                                        <div class="form-group {{ $errors->first('ticket_price', 'has-error') }}">
                                            <label class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-addon text-dark-muted">
                                                    $
                                                </span>
                                                <input name="ticket_price" class="form-control" placeholder="0.00" value="{{ old('ticket_price') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="form-group {{ $errors->first('ticket_quantity', 'has-error') }}">
                                            <label class="form-label">Ticket Quantity</label>
                                            <input name="ticket_quantity" class="form-control" placeholder="250" value="{{ old('ticket_quantity') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container text-right">
            <button type="submit" class="btn btn-primary">Add Concert</button>
        </div>
    </form>

    <footer class="p-xs-y-6 text-light-muted">
        <div class="container">
            <p class="text-center">&copy; TicketBeast {{ date('Y') }}</p>
        </div>
    </footer>
@endsection
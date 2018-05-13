@extends('layouts.master')

@section('body')
<div class="full-height">
    <div class="w-1/2 mx-auto p-8">
        <div class="bg-white rounded">
            <div>
                <h1 class="font-bold">Order Summary</h1>
                <span class="text-base">{{ $order->subtitle }}</span>
            </div>
            
            <div class="px-8">
                <div class="flex">
                    <div class="w-3 h-3 mr-3">
                        @icon ('calendar')
                    </div>
                    <div class="">
                        <span class="">{{ $order->email }}</span>
                        <span class="">Order Total: ${{ number_format($order->amount / 100, 2) }}</span>
                        <a href="/orders/{{ $order->confirmation_number }}">{{ $order->confirmation_number }}</a> 
                        <span class="">**** **** **** {{ $order->card_last_four }}</span>
                    </div>
                </div>
            </div>
            
            @foreach($order->tickets as $ticket)
            <div class="">
                <span class="">{{ $ticket->code }}</span>
                <span class="">{{ $ticket->concert->title }}</span>
                <span class="">{{ $ticket->concert->subtitle }}</span>
                <span class="">{{ $ticket->concert->date->format('Y-m-d H:i') }}</span>
                <span class="">{{ $ticket->concert->date->format('l, F jS, Y') }}</span>
                <span class="">{{ $ticket->concert->date->format('g:ia') }}</span>
                <span class="">{{ $ticket->concert->venue }}</span>
                <span class="">{{ $ticket->concert->venue_address }}</span>
                <span class="">{{ $ticket->concert->city }}</span>
                <span class="">{{ $ticket->concert->state }}</span>
                <span class="">{{ $ticket->concert->zip }}</span>
            </div>
            @endforeach
            <div class="text-center">
                <p>Powered by TicketBeast</p>
            </div>
            
        </div>
    </div>
</div>
</div>
@endsection

@push('beforeScripts')
<script src="https://checkout.stripe.com/checkout.js"></script>
@endpush
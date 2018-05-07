@extends('layouts.master')

@section('body')
<div class="full-height">
        <div class="w-1/2 mx-auto p-8">
            <div class="bg-white rounded">
                <div>
                    <h1 class="font-bold">{{ $concert->title }}</h1>
                    <span class="text-base">{{ $concert->subtitle }}</span>
                </div>

                <div class="px-8">
                    <div class="flex">
                        <div class="w-3 h-3 mr-3">
                            @icon ('calendar')
                        </div>
                        <div class="">
                            <span class="">{{ $concert->formatted_date }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="flex">
                        <div class="w-3 h-3 mr-3">
                            @icon ('time')
                        </div>
                        <div class="">
                            <span class="">Doors at {{ $concert->formatted_start_time }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="flex">
                        <div class="w-3 h-3 mr-3">
                            @icon ('currency-dollar')
                        </div>
                        <div class="">
                            <span class="">{{ $concert->ticket_price_in_dollars }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="flex">
                        <div class="w-3 h-3 mr-3">
                            @icon ('location')
                        </div>
                        <div class="">
                            <span class="">{{ $concert->venue }}</span>
                            <span class="">{{ $concert->venue_address }}</span>
                            <span class="">{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="flex">
                        <div class="w-3 h-3 mr-3">
                            @icon ('information-solid')
                        </div>
                        <div class="">
                            <span class="">Additional Information</span>
                            <span class="">{{ $concert->additional_information}}</span>
                        </div>
                    </div>
                </div>
                <div class="border-t">
                    <div class="card-section">
                        <ticket-checkout
                            :concert-id="{{ $concert->id }}"
                            concert-title="{{ $concert->title }}"
                            :price="{{ $concert->ticket_price }}"
                        ></ticket-checkout>
                    </div>
                </div>

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
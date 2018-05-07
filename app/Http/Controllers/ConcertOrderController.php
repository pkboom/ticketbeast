<?php

namespace App\Http\Controllers;

use App\ConcertOrder;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Concert;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\PaymentFailedException;

class ConcertOrderController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Concert $concert)
    {
        request()->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
            ]);

        try {
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            // $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            // $order = $reservation->complete();

            $order = $reservation->complete($this->paymentGateway, request('payment_token'));
        } catch (PaymentFailedException $e) {
            $reservation->cancel();

            return response([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (request()->wantsJson()) {
            return $order;
        }

        return response([], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConcertOrder  $concertOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ConcertOrder $concertOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConcertOrder  $concertOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ConcertOrder $concertOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConcertOrder  $concertOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConcertOrder $concertOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConcertOrder  $concertOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConcertOrder $concertOrder)
    {
        //
    }
}

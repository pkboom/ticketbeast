<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Concert;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\PaymentFailedException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationEmail;

class ConcertOrderController extends Controller
{
    public function store(Concert $concert, PaymentGateway $paymentGateway)
    {
        request()->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
            ]);

        try {
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            $order = $reservation->complete($paymentGateway, request('payment_token'));

            Mail::to($order->email)->send(new OrderConfirmationEmail($order));
        } catch (PaymentFailedException $e) {
            $reservation->cancel();

            return response([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (request()->wantsJson()) {
            return $order;
        }

        return response([], 201);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Billing\StripePaymentGateway;
use App\Billing\PaymentGateway;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\TicketCodeGenerator;
use App\HashidsTicketCodeGenerator;
use Laravel\Dusk\DuskServiceProvider;
use App\InvitationCodeGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        // $this->app->alias(StripePaymentGateway::class, PaymentGateway::class);

        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);

        $this->app->bind(TicketCodeGenerator::class, function () {
            return new HashidsTicketCodeGenerator(config('ticketbeast.ticket_code_salt'));
        });

        $this->app->bind(InvitationCodeGenerator::class, RandomOrderConfirmationNumberGenerator::class);
    }
}

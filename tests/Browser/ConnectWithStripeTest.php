<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConnectWithStripeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_connects_a_stripe_account()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/backstage/stripe-connect/connect')
                    ->clickLink('Connect with Stripe')
                    ->assertUrlIs('https://connect.stripe.com/oauth/authorize')
                    ->assertQueryStringHas('response_type', 'code')
                    ->assertQueryStringHas('client_id', config('services.stripe.client_id'))
                    ->assertQueryStringHas('scope', 'read_write')
                    ->clickLink('Skip this account form')
                    ->assertRouteIs('backstage.concerts.index');

            tap($user->fresh(), function ($user) {
                $this->assertNotNull($user->stripe_account_id);
                $this->assertNotNull($user->stripe_access_token);

                \Stripe\Stripe::setApiKey($user->stripe_access_token);

                $connectedAccount = \Stripe\Account::retrieve();

                $this->assertEquals($connectedAccount->id, $user->stripe_account_id);
            });
        });
    }
}

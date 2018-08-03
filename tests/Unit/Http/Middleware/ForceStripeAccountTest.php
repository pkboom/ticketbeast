<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Middleware\ForceStripeAccount;
use Illuminate\Foundation\Testing\TestResponse;

class ForceStripeAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_without_a_stripe_account_are_forced_to_connect_with_stripe()
    {
        $this->signIn(['stripe_account_id' => null]); // no stripe account

        $middleware = new ForceStripeAccount;

        $response = new TestResponse($middleware->handle(new Request, function ($request) {
            $this->fail('Next middleware was called when it should not have been.');
        }));

        $response->assertRedirect(route('backstage.stripe-connect.connect'));
    }

    /** @test */
    public function users_with_a_stripe_account_can_continue()
    {
        $this->signIn([
            'stripe_account_id' => '1234'
        ]);

        $response = false;

        $middleware = new ForceStripeAccount;

        $this->assertEquals(
            'valid stripe id',
            $middleware->handle(new Request, function ($request) {
                return 'valid stripe id';
            })
        );
    }
}

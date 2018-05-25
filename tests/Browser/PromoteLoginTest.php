<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class PromoteLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function logging_in_successfully()
    {
        factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'jane@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->assertPathIs('/backstage/concerts');
        });
    }

    /** @test */
    public function logging_in_with_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'john@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->assertPathIs('/login')
                ->assertInputValue('email', 'john@example.com')
                ->assertSee('credentials do not match');
        });
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        Schema::enableForeignKeyConstraints();
    }

    protected function signIn($user = null)
    {
        $user = $user ?? create(User::class);

        return $this->actingAs($user);
    }

    // protected function signInAdmin($admin = null)
    // {
    //     $admin = $admin ?? create(User::class);

    //     config(['council.administrators' => [$admin->email]]);

    //     return $this->actingAs($admin);
    // }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost';

    protected function setUp()
    {
        parent::setUp();

        Schema::enableForeignKeyConstraints();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        EloquentCollection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contains the specified value.');
        });

        EloquentCollection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), 'Failed asserting that the collection does not contain the specified value.');
        });

        EloquentCollection::macro('assertEquals', function ($items) {
            Assert::assertEquals($this->count(), $items->count());

            $this->zip($items)->each(function ($pair) {
                [$a, $b] = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
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

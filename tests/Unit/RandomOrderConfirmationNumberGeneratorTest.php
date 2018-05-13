<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\RandomOrderConfirmationNumberGenerator;

class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    /** @test */
    public function must_be_24_characters_long()
    {
        $confirmationNumber = (new RandomOrderConfirmationNumberGenerator)->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    public function can_only_contain_uppercase_letters_and_numbers()
    {
        $confirmationNumber = (new RandomOrderConfirmationNumberGenerator)->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    public function cannot_contain_ambiguous_characters()
    {
        $confirmationNumber = (new RandomOrderConfirmationNumberGenerator)->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
    }

    /** @test */
    public function must_be_unique()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumbers = array_map(function ($value) use ($generator) {
            return $generator->generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($confirmationNumbers));
    }
}

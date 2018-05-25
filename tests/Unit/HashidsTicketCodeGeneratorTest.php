<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\HashidsTicketCodeGenerator;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->hashid = (new HashidsTicketCodeGenerator('Hash'))
                        ->generateFor((object)['id' => 1]);
    }

    /** @test */
    public function it_should_be_at_least_6_characters_long()
    {
        $this->assertTrue(strlen($this->hashid) >= 6);
    }

    /** @test */
    public function can_only_contain_uppercase_letters()
    {
        $this->assertRegExp('/^[A-Z]+$/', $this->hashid);
    }

    /** @test */
    public function ticket_codes_for_the_same_ticket_id_is_the_same()
    {
        $hashid = (new HashidsTicketCodeGenerator('Hash'))->generateFor((object)['id' => 1]);

        $this->assertEquals($this->hashid, $hashid);
    }

    /** @test */
    public function tickets_code_with_different_salts_must_be_different()
    {
        $hashid = new HashidsTicketCodeGenerator('Hash');

        $array = array_map(function ($key) use ($hashid) {
            return $hashid->generateFor((object)['id' => $key]);
        }, range(1, 100));

        $this->assertCount(100, array_unique($array));
    }
}

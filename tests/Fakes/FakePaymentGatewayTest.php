<?php

namespace Tests\Fakes;

use Tests\TestCase;
use Tests\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function retrieving_charges()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge("john@example.com", 25000, $paymentGateway->validTestToken(), "Example DescriptionA");
        $paymentGateway->charge("jane@example.org", 5000, $paymentGateway->validTestToken(), "Example DescriptionB");
        $paymentGateway->charge("ricardo@milos.net", 7500, $paymentGateway->validTestToken(), "Example DescriptionC");

        $charges = $paymentGateway->charges();
        $this->assertCount(3,  $charges);

        $this->assertEquals('john@example.com', $charges[0]->email());
        $this->assertEquals(25000, $charges[0]->amount());
        $this->assertEquals('Example DescriptionA', $charges[0]->description());

        $this->assertEquals('jane@example.org', $charges[1]->email());
        $this->assertEquals(5000, $charges[1]->amount());
        $this->assertEquals('Example DescriptionB', $charges[1]->description());

        $this->assertEquals('ricardo@milos.net', $charges[2]->email());
        $this->assertEquals(7500, $charges[2]->amount());
        $this->assertEquals('Example DescriptionC', $charges[2]->description());


        $this->assertEquals(5000, $charges[1]->amount());

        $this->assertEquals(7500, $charges[2]->amount());
    }

}

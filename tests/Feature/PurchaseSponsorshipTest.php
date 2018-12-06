<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\FakePaymentGateway;
use App\Sponsorable;
use App\SponsorableSlot;
use App\Sponsorship;
use Illuminate\Database \Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\PaymentGateway;

class PurchaseSponsorshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function purchasing_available_sponsorship_slots()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);

        $slotA = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);
        $slotB = factory(SponsorableSlot::class)->create(['price' => 30000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2) ]);
        $slotC = factory(SponsorableSlot::class)->create(['price' => 25000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3) ]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'DigitalTechnoSoft Inc.',
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotC->getKey(),
            ]
        ]);

        $response->assertStatus(201);

        $this->assertEquals(1, Sponsorship::count());
        $sponsorship = Sponsorship::first();

        $this->assertEquals('john@example.com', $sponsorship->email);
        $this->assertEquals('DigitalTechnoSoft Inc.', $sponsorship->company_name);
        $this->assertEquals(75000, $sponsorship->amount);

        $this->assertEquals($sponsorship->getKey(), $slotA->fresh()->sponsorship_id);
        $this->assertEquals($sponsorship->getKey(), $slotC->fresh()->sponsorship_id);

        $this->assertNull($slotB->fresh()->sponsorship_id);

        $this->assertCount(1, $paymentGateway->charges());
        $charge = $paymentGateway->charges()->first();
        $this->assertEquals('john@example.com', $charge->email());
        $this->assertEquals(75000, $charge->amount());
        $this->assertEquals('Full Stack Radio sponsorship', $charge->description());


    }
}

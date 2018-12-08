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
            'payment_token' => $paymentGateway->validTestToken(),
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

    /** @test */
    public function sponsorship_is_not_created_if_payment_token_cannot_be_charged()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);

        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'DigitalTechnoSoft Inc.',
            'payment_token' => 'not-a-valid-token',
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function company_name_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => '',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('company_name');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => '',
            'company_name' => 'ExampleSoft',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function email_must_look_like_an_email()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'not-a-valid-email',
            'company_name' => 'ExampleSoft',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function payment_token_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => null,
            'sponsorable_slots' => [
                $slot->getKey(),
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_token');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function sponsorable_slots_is_required()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => null,
            'sponsorable_slots' => null,

        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function sponsorable_slots_must_be_an_array()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => null,
            'sponsorable_slots' => 'not-an-array',

        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function at_least_one_sponsorable_slot_must_be_provided()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => null,
            'sponsorable_slots' => [],

        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }


    /** @test */
    public function sponsorable_slots_must_be_unique()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;


        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio', 'name' => 'Full Stack Radio']);
        $slot = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => null,
            'sponsorable_slots' => [
                $slot->getKey(),
                $slot->getKey(),
            ],

        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('sponsorable_slots');

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }

    /** @test */
    public function cannot_sponsor_another_sponsorables_slots()
    {
        $paymentGateway = $this->app->instance(PaymentGateway::class, new FakePaymentGateway)        ;

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);
        $otherSponsorable = factory(Sponsorable::class)->create(['slug' => 'laravel-news']);

        $slotA = factory(SponsorableSlot::class)->create(['price' => 50000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1) ]);
        $slotB = factory(SponsorableSlot::class)->create(['price' => 30000, 'sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2) ]);
        $otherSlot = factory(SponsorableSlot::class)->create(['price' => 25000, 'sponsorable_id' => $otherSponsorable, 'publish_date' => now()->addMonths(3) ]);

        $response = $this->withExceptionHandling()->postJson('/full-stack-radio/sponsorships', [
            'email' => 'john@example.com',
            'company_name' => 'ExampleSoft',
            'payment_token' => $paymentGateway->validTestToken(),
            'sponsorable_slots' => [
                $slotA->getKey(),
                $slotB->getKey(),
                $otherSlot->getKey(),
            ]
        ]);

        $response->assertStatus(404);

        $this->assertEquals(0, Sponsorship::count());
        $this->assertNull($slotA->fresh()->sponsorship_id);
        $this->assertNull($slotB->fresh()->sponsorship_id);
        $this->assertNull($otherSlot->fresh()->sponsorship_id);
        $this->assertCount(0, $paymentGateway->charges());
    }
}

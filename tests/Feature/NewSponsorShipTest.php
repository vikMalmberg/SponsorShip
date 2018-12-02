<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Sponsorable;
use App\SponsorableSlot;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewSponsorShipTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function viewing_the_new_sponsorship_page()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $sponsorableSlots = new EloquentCollection([
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
        ]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $response->assertTrue($response->data('sponsorable')->is($sponsorable));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }
}
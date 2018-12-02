<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Sponsorable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SponsorableTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function finding_a_sponsorable_by_slug()
    {

        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $foundSponsorable = Sponsorable::findOrFailBySlug('full-stack-radio');

        $this->assertTrue($foundSponsorable->is($sponsorable));
    }
    /** @test */
    public function an_exception_is_thrown_if_a_sponsorable_cannot_be_found_by_slug()
    {
        $this->expectException(ModelNotFoundException::class);
        Sponsorable::findOrFailBySlug('slug-that-does-not-exist');
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sponsorable;

class SponsorableSponsorshipsController extends Controller
{

    public function new($slug)
    {

        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorableSlots = $sponsorable->slots;

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots
        ]);
    }
}

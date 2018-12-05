<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sponsorable;
use App\SponsorableSlot;
use App\Sponsorship;

class SponsorableSponsorshipsController extends Controller
{
    private $paymentGateway;

    public function __construct($paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function new($slug)
    {

        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorableSlots = $sponsorable->slots()
                                        ->sponsorable()
                                        ->orderBy('publish_date')
                                        ->get();

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots
        ]);
    }

    public function store($slug)
    {

        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorship = Sponsorship::create([
            'email' => request('email'),
            'company_name' => request('company_name'),
        ]);

        $slots = SponsorableSlot::whereIn('id', request('sponsorable_slots'))->get();

        $this->paymentGateway->charge(request('email'), $slots->sum('price'), 'tok', "{$sponsorable->name} sponsorship");

        $slots->each->update(['sponsorship_id' => $sponsorship->id]);

        return response()->json([],201);
    }
}

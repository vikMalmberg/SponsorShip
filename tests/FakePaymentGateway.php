<?php

namespace Tests;

use Illuminate\Support\Collection;
Use App\Charge;

class FakePaymentGateway
{
    private $charges;


    public function __construct()
    {
        $this->charges = new Collection;
    }

    public function charge($email, $amount, $token, $description)
    {
        $this->charges->push(new Charge($email, $amount, $description));
    }

    public function validTestToken()
    {
        return "valid_test_token";
    }

    public function charges()
    {
        return $this->charges;
    }


}

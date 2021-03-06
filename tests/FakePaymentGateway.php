<?php

namespace Tests;

use Illuminate\Support\Collection;
use App\Charge;
use App\Exceptions\PaymentFailedException;

class FakePaymentGateway
{
    private $charges;


    public function __construct()
    {
        $this->charges = new Collection;
    }

    public function charge($email, $amount, $token, $description)
    {
        if ($token !== $this->validTestToken()) {
            throw new PaymentFailedException;
        }
        return tap(new Charge($email, $amount, $description), function($charge){
            $this->charges->push($charge);
        });

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

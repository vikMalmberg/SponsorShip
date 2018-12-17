<?php

namespace App\Providers;

use App\PaymentGateway;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SponsorableSponsorshipsController;
use Tests\FakePaymentGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(PaymentGateway::class, function() {
            return new FakePaymentGateway;
        });

        $this->app->bind(SponsorableSponsorshipsController::class, function() {
            return new SponsorableSponsorshipsController($this->app[PaymentGateway::class]);
        });
    }
}

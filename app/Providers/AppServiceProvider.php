<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SponsorableSponsorshipsController;
use App\PaymentGateway;

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
        $this->app->bind(SponsorableSponsorshipsController::class, function() {
            return new SponsorableSponsorshipsController($this->app[PaymentGateway::class]);
        });
    }
}

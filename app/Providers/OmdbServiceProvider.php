<?php

namespace App\Providers;

use App\Services\OmdbService;
use Illuminate\Support\ServiceProvider;

class OmdbServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OmdbService::class, function ($app) {
            return new OmdbService();
        });
    }

    public function boot()
    {
        //
    }
} 
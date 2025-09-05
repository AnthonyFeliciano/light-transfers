<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\{AuthorizationClientContract, TransferServiceContract};
use App\Services\{AuthorizationClient, TransferService};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthorizationClientContract::class, AuthorizationClient::class);
        $this->app->bind(TransferServiceContract::class, function ($app) {
        return new TransferService($app->make(AuthorizationClientContract::class));
    });
    }

    public function boot(): void
    {
        
    }
}

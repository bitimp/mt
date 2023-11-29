<?php

namespace App\Providers;

use App\Contracts\Services\LoyaltyPointsManager as LoyaltyPointsManagerContract;
use App\Services\LoyaltyPointsManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LoyaltyPointsManagerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(LoyaltyPointsManagerContract::class, function (Application $app) {
            return new LoyaltyPointsManager();
        });
    }

    public function provides(): array
    {
        return [LoyaltyPointsManagerContract::class];
    }
}

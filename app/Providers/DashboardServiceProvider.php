<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Dashboard\DashboardOverviewRepositoryInterface;
use App\Repositories\Dashboard\DashboardOverviewRepository;
use App\Services\Dashboard\DashboardOverviewService;

class DashboardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DashboardOverviewRepositoryInterface::class, DashboardOverviewRepository::class);
        
        $this->app->singleton(DashboardOverviewService::class, function ($app) {
            return new DashboardOverviewService(
                $app->make(DashboardOverviewRepositoryInterface::class)
            );
        });
    }

    public function boot()
    {
        //
    }
} 
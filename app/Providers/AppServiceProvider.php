<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Settings\UserRepositoryInterface;
use App\Repositories\Settings\UserRepository;
use App\Models\Production\SalesGasMeteringHourly;
use App\Observers\SalesGasMeteringHourlyObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}

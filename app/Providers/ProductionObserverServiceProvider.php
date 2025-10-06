<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Production\WellProduction;
use App\Models\Production\GasSalesMetering;
use App\Observers\WellProductionObserver;
use App\Observers\GasSalesMeteringObserver;

class ProductionObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
    }
}

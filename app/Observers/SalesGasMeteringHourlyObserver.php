<?php

namespace App\Observers;

use App\Models\Production\SalesGasMeteringHourly;
use App\Services\SalesGasMeteringService;
use Illuminate\Support\Facades\Log;
class SalesGasMeteringHourlyObserver
{
    protected SalesGasMeteringService $service;
    
    public function __construct(SalesGasMeteringService $service)
    {
        $this->service = $service;
    }
    
    /**
     * Handle the created event.
     */
    public function created(SalesGasMeteringHourly $hourly): void
    {
        $this->updateDailySummary($hourly);
    }
    
    /**
     * Handle the updated event.
     */
    public function updated(SalesGasMeteringHourly $hourly): void
    {
        $this->updateDailySummary($hourly);
    }
    
    /**
     * Handle the deleted event.
     */
    public function deleted(SalesGasMeteringHourly $hourly): void
    {
        $this->updateDailySummary($hourly);
    }
    
    /**
     * Update daily summary when hourly data changes
     */
    protected function updateDailySummary(SalesGasMeteringHourly $hourly): void
    {
        try {
            // Check if daily summary is locked
            $daily = $this->service->getDailySummary($hourly->vessel_id, $hourly->date);
            
            if ($daily && $daily->isLocked()) {
                Log::warning('Attempted to update locked daily summary', [
                    'vessel_id' => $hourly->vessel_id,
                    'date' => $hourly->date,
                    'daily_id' => $daily->id,
                ]);
                return;
            }
            
            // Trigger daily summary recalculation
            $this->service->updateDailySummary($hourly->vessel_id, $hourly->date);
            
            Log::info('Daily summary updated successfully', [
                'vessel_id' => $hourly->vessel_id,
                'date' => $hourly->date,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update daily summary', [
                'vessel_id' => $hourly->vessel_id,
                'date' => $hourly->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

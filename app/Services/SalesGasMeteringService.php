<?php
// app/Services/SalesGasMeteringService.php

namespace App\Services;

use App\Models\Production\SalesGasMeteringHourly;
use App\Models\Production\SalesGasMeteringDaily;
use App\Models\Production\SalesGasMeteringDailyFlowrate;
use App\Exceptions\LockedDataException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesGasMeteringService
{
    /**
     * Update or create daily summary from hourly readings
     */
    public function updateDailySummary(int $vesselId, string|Carbon $date): SalesGasMeteringDaily
    {
        // Ensure date is Carbon instance
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return DB::transaction(function () use ($vesselId, $date) {
            // Step 1: Calculate daily averages
            $dailyData = $this->calculateDailyAverages($vesselId, $date);
            
            // Step 2: Update or create daily summary
            $daily = SalesGasMeteringDaily::updateOrCreate(
                [
                    'vessel_id' => $vesselId,
                    'date' => $date->format('Y-m-d'),
                ],
                array_merge($dailyData, [
                    'last_calculated_at' => now(),
                ])
            );
            
            // Step 3: Update daily flowrates per buyer
            $this->updateDailyFlowrates($daily, $vesselId, $date);
            
            return $daily->fresh(['flowrates']);
        });
    }
    
    /**
     * Calculate daily averages from hourly readings
     */
    protected function calculateDailyAverages(int $vesselId, Carbon $date): array
    {
        $averages = SalesGasMeteringHourly::where('vessel_id', $vesselId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->select([
                DB::raw('COUNT(*) as total_readings'),
                DB::raw('AVG(pressure_psig) as pressure_psig'),
                DB::raw('AVG(temperature_f) as temperature_f'),
                DB::raw('AVG(h2o_lb_mmscf) as h2o_lb_mmscf'),
                DB::raw('AVG(co2_mol_pct) as co2_mol_pct'),
                DB::raw('AVG(ghv) as ghv'),
                DB::raw('AVG(specific_gravity) as specific_gravity'),
                DB::raw('AVG(ejgp_pressure_psig) as ejgp_pressure_psig'),
                DB::raw('AVG(total_flow_rate) as total_flow_rate'),
            ])
            ->first();
        
        // Calculate data completeness percentage
        $totalReadings = $averages->total_readings ?? 0;
        $dataCompletenessPct = ($totalReadings / 24) * 100;
        
        return [
            'pressure_psig' => $averages->pressure_psig,
            'temperature_f' => $averages->temperature_f,
            'h2o_lb_mmscf' => $averages->h2o_lb_mmscf,
            'co2_mol_pct' => $averages->co2_mol_pct,
            'ghv' => $averages->ghv,
            'specific_gravity' => $averages->specific_gravity,
            'ejgp_pressure_psig' => $averages->ejgp_pressure_psig,
            'total_flow_rate' => $averages->total_flow_rate,
            'total_readings' => $totalReadings,
            'data_completeness_pct' => round($dataCompletenessPct, 2),
        ];
    }
    
    /**
     * Update daily flowrates per buyer
     * 
     * ALL FLOWRATES USE AVERAGE (AVG) - NOT SUM
     * Reason: Flow rates are rates per unit time, daily average makes sense
     */
    protected function updateDailyFlowrates(
        SalesGasMeteringDaily $daily,
        int $vesselId,
        Carbon $date
    ): void {
        // Get all hourly metering IDs for this date
        $hourlyIds = SalesGasMeteringHourly::where('vessel_id', $vesselId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->pluck('id');
        
        if ($hourlyIds->isEmpty()) {
            return;
        }
        
        // Calculate flowrate AVERAGES per buyer (NOT SUM)
        // Example: If PKG has 24 hourly readings, we take AVG of those 24 values
        $flowrateAverages = DB::table('gas_sales_metering_hourly_flowrates')
            ->whereIn('gas_sales_metering_hourly_id', $hourlyIds)
            ->select([
                'buyer_id',
                DB::raw('AVG(primary_stream) as primary_stream'),
                DB::raw('AVG(backup_stream) as backup_stream'),
            ])
            ->groupBy('buyer_id')
            ->get();
        
        // Update or create daily flowrate for each buyer
        foreach ($flowrateAverages as $flowrate) {
            SalesGasMeteringDailyFlowrate::updateOrCreate(
                [
                    'gas_sales_metering_daily_id' => $daily->id,
                    'vessel_id' => $daily->vessel_id,
                    'buyer_id' => $flowrate->buyer_id,
                ],
                [
                    'primary_stream' => $flowrate->primary_stream,
                    'backup_stream' => $flowrate->backup_stream,
                ]
            );
        }
    }
    
    /**
     * Get daily summary
     */
    public function getDailySummary(int $vesselId, string|Carbon $date): ?SalesGasMeteringDaily
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return SalesGasMeteringDaily::where('vessel_id', $vesselId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->with(['flowrates.buyer'])
            ->first();
    }
    
    /**
     * Approve daily summary
     */
    public function approveDailySummary(int $dailyId, int $approvedBy): SalesGasMeteringDaily
    {
        $daily = SalesGasMeteringDaily::findOrFail($dailyId);
        
        if ($daily->isLocked()) {
            throw new LockedDataException('Cannot approve: Daily summary is locked');
        }
        
        if (!$daily->canBeApproved()) {
            throw new \Exception('Cannot approve: Data is incomplete or already approved');
        }
        
        $daily->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
        
        return $daily->fresh();
    }
    
    /**
     * Lock daily summary (permanent)
     */
    public function lockDailySummary(int $dailyId): SalesGasMeteringDaily
    {
        $daily = SalesGasMeteringDaily::findOrFail($dailyId);
        
        if ($daily->isLocked()) {
            throw new LockedDataException('Daily summary is already locked');
        }
        
        $daily->update([
            'status' => 'locked',
            'locked_at' => now(),
        ]);
        
        return $daily->fresh();
    }
    
    /**
     * Unlock daily summary (admin only)
     */
    public function unlockDailySummary(int $dailyId): SalesGasMeteringDaily
    {
        $daily = SalesGasMeteringDaily::findOrFail($dailyId);
        
        $daily->update([
            'status' => 'approved',
            'locked_at' => null,
        ]);
        
        return $daily->fresh();
    }
}
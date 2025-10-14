<?php
namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Vessel;
use App\Models\User;

class VesselOperation extends Model
{
    protected $table = 'vessel_operations';

    protected $fillable = [
        'vessel_id',
        'date',
        
        // Gas Operations
        'inlet_gas_mmscfd',
        'total_sales_gas_mmscfd',
        'fuel_gas_mmscfd',
        'flare_hp_mmscfd',
        'flare_lp_mmscfd',
        'total_flare_gas_mmscfd',
        'gas_export_uptime',
        'gas_export_uptime_percent',
        'inlet_pressure_psi',
        'inlet_temp_f',
        'outlet_pressure_psi',
        'outlet_temp_f',
        
        // Condensate Operations
        'condensate_produced_lts',
        'condensate_produced_bbls',
        'condensate_skimmed_bbls',
        'condensate_production_total_bbls',
        'condensate_stock_bbls',
        'condensate_consumed_gtg_bbls',
        'condensate_temp_f',
        'condensate_uptime',
        'condensate_uptime_percent',
        
        // Diesel Fuel
        'diesel_fuel_mopu_ltr',
        'diesel_fuel_hcml_ltr',
        
        // Water Operations
        'produced_water_total_bbls',
        'produced_water_offspec_bbls',
        'produced_water_overboard_bbls',
        'water_oiw_content_ppm',
        
        // Metadata
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'gas_export_uptime' => 'datetime:H:i:s',
        'condensate_uptime' => 'datetime:H:i:s',
        'inlet_gas_mmscfd' => 'decimal:4',
        'total_sales_gas_mmscfd' => 'decimal:4',
        'fuel_gas_mmscfd' => 'decimal:4',
        'flare_hp_mmscfd' => 'decimal:4',
        'flare_lp_mmscfd' => 'decimal:4',
        'total_flare_gas_mmscfd' => 'decimal:4',
        'gas_export_uptime_percent' => 'decimal:2',
        'condensate_uptime_percent' => 'decimal:2',
    ];

    /**
     * Relationship: Vessel
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    /**
     * Relationship: Well Flows (dynamic wells)
     */
    public function wellFlows(): HasMany
    {
        return $this->hasMany(VesselOperationWellFlow::class, 'vessel_operation_id');
    }

    /**
     * Relationship: Recorded By User
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Accessor: Get total well flow (sum of all wells)
     */
    public function getTotalWellFlowAttribute(): float
    {
        return $this->wellFlows->sum('gas_flow_rate_mmscfd');
    }

    /**
     * Accessor: Check if well balance is correct
     */
    public function getIsBalancedAttribute(): bool
    {
        if (!$this->inlet_gas_mmscfd || $this->wellFlows->isEmpty()) {
            return true;
        }
        
        $difference = abs($this->inlet_gas_mmscfd - $this->total_well_flow);
        return $difference < 0.01; // Tolerance 0.01 MMSCFD
    }

    /**
     * Scope: Filter by vessel
     */
    public function scopeByVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class DailyProduction extends Model
{
    use HasFactory;

    protected $table = 'production_daily';

    protected $fillable = [
        'vessel_id',
        'date',
        'gas_export_uptime_hours',
        'gas_produced_total_mmscf',
        'gas_exported_mmscf',
        'fuel_gas_mmscf',
        'gas_production_target_mmscf',
        'gas_export_pressure_psig',
        'gas_export_temp_f',
        'gas_flared_hp_mmscf',
        'gas_flared_lp_mmscf',
        'gas_flaring_total_mmscf',
        'gas_produced_mtd_mmscf',
        'gas_production_target_mtd_mmscf',
        'gas_export_mtd_mmscf',
        'gas_produced_cumulative_mmscf',
        'gas_export_cumulative_mmscf',
        'condensate_uptime_hours',
        'condensate_produced_bbls',
        'condensate_skimmed_bbls',
        'condensate_production_total_bbls',
        'condensate_production_cumulative_bbls',
        'condensate_temp_f',
        'condensate_used_by_gtg_bbls',
        'condensate_skimmed_tag',
        'condensate_temp_tag',
        'condensate_gtg_tag',
        'produced_water_total_bbls',
        'produced_water_offspec_bbls',
        'produced_water_overboard_bbls',
        'water_oiw_content_ppm',
        'water_overboard_tag',
        'multiphase_flow_total_mmscf',
        'multiphase_flow_mtd_mmscf',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
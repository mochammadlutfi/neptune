<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class VesselOperation extends Model
{
    use HasFactory;

    protected $table = 'vessel_operations';

    protected $fillable = [
        'vessel_id',
        'date',
        'status',
        'mac_mmscf',
        'inlet_gas_mmscf',
        'condencate_produced_lts',
        'condensate_skimmed_t43',
        'condensate_production_total_bbls',
        'condensate_stock_total_bbls',
        'condensate_consumed_gtg_bbls',
        'diesel_fuel_vessel_consumption',
        'diesel_fuel_client_consumtion',
        'produce_water_total',
        'discharge_water_overload',
        'oil_in_water_content',
        'export_gas_from_vessel',
        'average_ejgp_pressure',
        'fuel_gas',
        'total_flare_gas_lp',
        'total_flare_gas_hp',
        'total_flare_gas',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'datetime',
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
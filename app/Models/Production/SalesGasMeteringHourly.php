<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class SalesGasMeteringHourly extends Model
{
    use HasFactory;

    protected $table = 'gas_sales_metering_hourly';

    protected $fillable = [
        'vessel_id',
        'date',
        'time',
        'pressure_psig',
        'temperature_f',
        'h2o_content_lb_mmscf',
        'hcdp_temp',
        'co2_content_mol_pct',
        'heating_value_btu_scf',
        'specific_gravity',
        'ejgp_pressure_psig',
        'total_flowrates',
        'status',
        'recorded_by',
    ];
    
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_uid');
    }

    public function flowrates()
    {
        return $this->hasMany(SalesGasMeteringHourlyFlowrate::class, 'gas_sales_metering_hourly_id');
    }
}
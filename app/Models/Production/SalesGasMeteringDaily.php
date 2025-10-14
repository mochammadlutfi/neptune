<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class SalesGasMeteringDaily extends Model
{
    use HasFactory;

    protected $table = 'gas_sales_metering_daily';

    protected $fillable = [
        'vessel_id',
        'date',
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
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function flowrates()
    {
        return $this->hasMany(SalesGasMeteringDailyFlowrate::class, 'gas_sales_metering_daily_id');
    }
}
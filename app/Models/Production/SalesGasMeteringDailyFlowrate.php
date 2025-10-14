<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\Master\GasBuyer;

class SalesGasMeteringDailyFlowrate extends Model
{
    use HasFactory;

    protected $table = 'gas_sales_metering_daily_flowrates';

    protected $fillable = [
        'gas_sales_metering_daily_id',
        'vessel_id',
        'buyer_id',
        'backup_stream',
        'primary_stream',
    ];

    public function metering()
    {
        return $this->belongsTo(SalesGasMeteringDaily::class, 'gas_sales_metering_daily_id');
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function buyer()
    {
        return $this->belongsTo(GasBuyer::class, 'buyer_id');
    }
}
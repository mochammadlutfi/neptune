<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Well extends Model
{
    use HasFactory;

    protected $table = 'wells';

    protected $fillable = [
        'vessel_id',
        'code',
        'name',
        'type',
        'max_oil_rate',
        'max_gas_rate',
        'max_water_rate',
        'status',
    ];

    
    protected $casts = [
        'max_oil_rate' => 'float',
        'max_gas_rate' => 'float',
        'max_water_rate' => 'float',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }


    public function scopeByVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }
}

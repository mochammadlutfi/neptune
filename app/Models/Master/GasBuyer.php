<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;

class GasBuyer extends Model
{
    use HasFactory;

    protected $table = 'gas_buyers';

    protected $fillable = [
        'vessel_id',
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }
    
    public function scopeByVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }


}

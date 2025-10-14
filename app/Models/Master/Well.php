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
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function scopeByVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }
}

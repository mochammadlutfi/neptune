<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;

class SalesGasAllocation extends Model
{
    use HasFactory;

    protected $table = 'gas_allocations';

    protected $fillable = [
        'vessel_id',
        'date',
        'total',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function lines()
    {
        return $this->hasMany(SalesGasAllocationLine::class, 'gas_allocation_id');
    }
}
<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class SalesGasAllocation extends Model
{
    use HasFactory;

    protected $table = 'gas_allocations';

    protected $fillable = [
        'vessel_id',
        'date',
        'total',
        'created_uid',
        'status',
        'remarks',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function lines()
    {
        return $this->hasMany(SalesGasAllocationLine::class, 'allocation_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_uid');
    }

}
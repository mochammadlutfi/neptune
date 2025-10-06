<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\GasBuyer;

class SalesGasAllocationLine extends Model
{
    use HasFactory;

    protected $table = 'gas_allocation_lines';

    protected $fillable = [
        'gas_allocation_id',
        'gas_buyer_id',
        'allocation',
    ];

    public function allocation()
    {
        return $this->belongsTo(SalesGasAllocation::class, 'gas_allocation_id');
    }

    public function buyer()
    {
        return $this->belongsTo(GasBuyer::class, 'gas_buyer_id');
    }
}
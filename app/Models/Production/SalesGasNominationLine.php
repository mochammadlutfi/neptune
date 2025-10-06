<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\GasBuyer;

class SalesGasNominationLine extends Model
{
    use HasFactory;

    protected $table = 'gas_nomination_lines';

    protected $fillable = [
        'gas_nomination_id',
        'buyer_id',
        'date',
        'nomination',
        'confirmed',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function nomination()
    {
        return $this->belongsTo(SalesGasNomination::class, 'gas_nomination_id');
    }

    public function buyer()
    {
        return $this->belongsTo(GasBuyer::class, 'buyer_id');
    }
}
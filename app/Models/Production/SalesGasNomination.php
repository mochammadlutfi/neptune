<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class SalesGasNomination extends Model
{
    use HasFactory;

    protected $table = 'gas_nomination';

    protected $fillable = [
        'vessel_id',
        'date',
        'nomination',
        'confirmed_at',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function lines()
    {
        return $this->hasMany(SalesGasNominationLine::class, 'gas_nomination_id');
    }
}
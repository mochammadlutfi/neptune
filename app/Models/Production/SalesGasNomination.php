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
        'name',
        'date',
        'nomination',
        'confirmed_at',
        'created_uid',
    ];

    protected $casts = [
        'date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    protected $appends = ['status_label'];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }


    public function lines()
    {
        return $this->hasMany(SalesGasNominationLine::class, 'gas_nomination_id');
    }
}
<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Well;
use App\Models\Master\Equipment;
use App\Models\Master\GasBuyer;
use App\Models\Master\Chemical;

use App\Models\User;
class Vessel extends Model
{
    use HasFactory;

    protected $table = 'vessels';

    protected $fillable = [
        'code',
        'name',
        'type',
        'oim_id',
        'client_name',
        'client_oim',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function wells()
    {
        return $this->hasMany(Well::class);
    }

    public function oim()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function gasBuyers()
    {
        return $this->hasMany(GasBuyer::class);
    }

    public function chemicals()
    {
        return $this->hasMany(Chemical::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}

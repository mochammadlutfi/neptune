<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class Tank extends Model
{
    use HasFactory;

    protected $table = 'tanks';

    protected $fillable = [
        'vessel_id',
        'code',
        'name',
        'type',
        'is_multiphase',
        'product_type',
        'capacity_bbls',
        'is_active',
        'created_uid',
    ];

    protected $casts = [
        'is_multiphase' => 'boolean',
        'is_active' => 'boolean',
        'capacity_bbls' => 'decimal:2',
    ];

    /**
     * Relationship dengan vessel
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    /**
     * Relationship dengan user yang membuat record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_uid');
    }

    /**
     * Scope untuk filter berdasarkan vessel
     */
    public function scopeByVessel($query, $vesselId)
    {
        return $query->where('vessel_id', $vesselId);
    }

    /**
     * Scope untuk filter berdasarkan type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk filter hanya tank aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter tank multiphase
     */
    public function scopeMultiphase($query, $isMultiphase = true)
    {
        return $query->where('is_multiphase', $isMultiphase);
    }
}
<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'vessel_id',
        'code',
        'tag',
        'name',
        'type',
        'category',
        'manufacturer',
        'model',
        'serial_number',
        'installation_date',
        'is_critical',
        'has_spare',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'is_critical' => 'boolean',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the equipment status records.
     */
    public function status()
    {
        return $this->hasMany(\App\Models\Equipment\EquipmentStatus::class);
    }

    /**
     * Get the latest equipment status.
     */
    public function latestStatus()
    {
        return $this->hasOne(\App\Models\Equipment\EquipmentStatus::class)->latest('status_date');
    }

    /**
     * Get the maintenance activities for this equipment.
     */
    public function maintenanceActivities()
    {
        return $this->hasMany(\App\Models\Equipment\MaintenanceActivity::class);
    }

    /**
     * Get the spare parts compatible with this equipment.
     */
    public function spareParts()
    {
        return $this->belongsToMany(\App\Models\Equipment\SparePart::class, 'equipment_spare_parts', 'equipment_id', 'spare_part_id');
    }

    /**
     * Get the spare parts usage for this equipment.
     */
    public function sparePartsUsage()
    {
        return $this->hasMany(\App\Models\Equipment\SparePartsUsage::class);
    }

    /**
     * Get the chemical operations performed on this equipment.
     */
    public function chemicalOperations()
    {
        return $this->hasMany(\App\Models\Equipment\ChemicalOperation::class);
    }

    /**
     * Scope a query to only include critical equipment.
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Scope a query to only include equipment by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include equipment by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the current operational status.
     */
    public function getCurrentStatusAttribute()
    {
        return $this->latestStatus?->operational_status ?? 'Unknown';
    }

    /**
     * Get the equipment age in years.
     */
    public function getAgeYearsAttribute()
    {
        if ($this->installation_date) {
            return now()->diffInYears($this->installation_date);
        }
        
        return null;
    }

    /**
     * Check if equipment has overdue maintenance.
     */
    public function getHasOverdueMaintenanceAttribute()
    {
        return $this->latestStatus && 
               $this->latestStatus->maintenance_due_date && 
               $this->latestStatus->maintenance_due_date < now();
    }
}

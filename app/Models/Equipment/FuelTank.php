<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class FuelTank extends Model
{
    use HasFactory;

    protected $table = 'fuel_tanks';

    protected $fillable = [
        'vessel_id',
        'tank_name',
        'tank_type',
        'fuel_type',
        'capacity_liters',
        'current_level_liters',
        'level_percentage',
        'density_kgm3',
        'temperature_c',
        'water_content_ppm',
        'last_reading_date',
        'location_description',
        'tank_status',
        'calibration_date',
        'next_calibration_date',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'capacity_liters' => 'decimal:2',
        'current_level_liters' => 'decimal:2',
        'level_percentage' => 'decimal:2',
        'density_kgm3' => 'decimal:2',
        'temperature_c' => 'decimal:2',
        'water_content_ppm' => 'decimal:2',
        'last_reading_date' => 'datetime',
        'calibration_date' => 'date',
        'next_calibration_date' => 'date',
    ];

    /**
     * Get the vessel that owns the fuel tank.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the user who recorded the last reading.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the fuel inventory records for this tank.
     */
    public function inventory()
    {
        return $this->hasMany(FuelInventory::class);
    }

    /**
     * Scope a query to only include tanks by fuel type.
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Scope a query to only include tanks by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('tank_status', $status);
    }

    /**
     * Scope a query to only include tanks with low fuel levels.
     */
    public function scopeLowLevel($query, $threshold = 20)
    {
        return $query->where('level_percentage', '<=', $threshold);
    }

    /**
     * Scope a query to only include tanks needing calibration.
     */
    public function scopeCalibrationDue($query)
    {
        return $query->where('next_calibration_date', '<=', now());
    }

    /**
     * Get the fuel level status based on percentage.
     */
    public function getFuelLevelStatusAttribute()
    {
        if ($this->level_percentage <= 10) {
            return 'Critical';
        } elseif ($this->level_percentage <= 25) {
            return 'Low';
        } elseif ($this->level_percentage <= 50) {
            return 'Medium';
        } else {
            return 'High';
        }
    }

    /**
     * Get the available capacity in liters.
     */
    public function getAvailableCapacityAttribute()
    {
        return $this->capacity_liters - $this->current_level_liters;
    }

    /**
     * Get the fuel mass in kilograms.
     */
    public function getFuelMassKgAttribute()
    {
        return $this->current_level_liters * ($this->density_kgm3 / 1000);
    }

    /**
     * Get the calibration status.
     */
    public function getCalibrationStatusAttribute()
    {
        if (!$this->next_calibration_date) {
            return 'No Schedule';
        }

        $daysUntilDue = now()->diffInDays($this->next_calibration_date, false);

        if ($daysUntilDue < 0) {
            return 'Overdue';
        } elseif ($daysUntilDue <= 30) {
            return 'Due Soon';
        } else {
            return 'Current';
        }
    }

    /**
     * Calculate estimated days until empty based on average consumption.
     */
    public function getEstimatedDaysUntilEmptyAttribute()
    {
        // This would need to be calculated based on historical consumption data
        // For now, return null - can be implemented with consumption tracking
        return null;
    }
}
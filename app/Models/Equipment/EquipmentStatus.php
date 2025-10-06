<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentStatus extends Model
{
    use HasFactory;

    protected $table = 'equipment_status';

    protected $fillable = [
        'vessel_id',
        'equipment_id',
        'reading_time',
        'shift',
        'operational_status',
        'running_hours',
        'parameters',
        'recorded_by',
    ];

    protected $casts = [
        'reading_time' => 'datetime',
        'running_hours' => 'decimal:2',
        'parameters' => 'array',
    ];

    public function vessel()
    {
        return $this->belongsTo(\App\Models\Master\Vessel::class);
    }

    public function equipment()
    {
        return $this->belongsTo(\App\Models\Master\Equipment::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    /**
     * Scope a query to only include status for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reading_time', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include status by operational status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('operational_status', $status);
    }

    /**
     * Scope a query to only include status by shift.
     */
    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    /**
     * Scope a query to only include running equipment.
     */
    public function scopeRunning($query)
    {
        return $query->where('operational_status', 'Running');
    }

    /**
     * Get the status indicator.
     */
    public function getStatusIndicatorAttribute()
    {
        switch ($this->operational_status) {
            case 'Running':
                return 'success';
            case 'Standby':
                return 'warning';
            case 'Shutdown':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get formatted running hours.
     */
    public function getFormattedRunningHoursAttribute()
    {
        if (!$this->running_hours) {
            return '0.00 hrs';
        }
        
        return number_format($this->running_hours, 2) . ' hrs';
    }

    /**
     * Check if equipment is operational.
     */
    public function getIsOperationalAttribute()
    {
        return in_array($this->operational_status, ['Running', 'Standby']);
    }
}

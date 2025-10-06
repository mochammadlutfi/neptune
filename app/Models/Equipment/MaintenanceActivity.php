<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Equipment;
use App\Models\Master\Vessel;
use App\Models\User;

class MaintenanceActivity extends Model
{
    use HasFactory;

    protected $table = 'maintenance_activities';

    protected $fillable = [
        'vessel_id',
        'equipment_id',
        'activity_date',
        'activity_type',
        'work_order_no',
        'description',
        'maintenance_hours',
        'downtime_hours',
        'completion_status',
        'priority_level',
        'cost_estimate',
        'actual_cost',
        'performed_by',
        'supervised_by',
        'remarks',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'maintenance_hours' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
        'cost_estimate' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    /**
     * Get the vessel that owns the maintenance activity.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the equipment that owns the maintenance activity.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who performed the maintenance.
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the user who supervised the maintenance.
     */
    public function supervisedBy()
    {
        return $this->belongsTo(User::class, 'supervised_by');
    }

    /**
     * Get the spare parts used in this maintenance activity.
     */
    public function sparePartsUsage()
    {
        return $this->hasMany(\App\Models\Equipment\SparePartsUsage::class);
    }

    /**
     * Scope a query to only include activities for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('activity_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include completed activities.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completion_status', 'Completed');
    }

    /**
     * Scope a query to only include activities by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Get the cost variance percentage.
     */
    public function getCostVarianceAttribute()
    {
        if ($this->cost_estimate > 0 && $this->actual_cost > 0) {
            return round((($this->actual_cost - $this->cost_estimate) / $this->cost_estimate) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Get the efficiency percentage based on planned vs actual hours.
     */
    public function getEfficiencyAttribute()
    {
        if ($this->maintenance_hours > 0) {
            $plannedHours = $this->maintenance_hours;
            $actualHours = $this->downtime_hours ?: $this->maintenance_hours;
            
            return round(($plannedHours / $actualHours) * 100, 2);
        }
        
        return 100;
    }
}
<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Equipment;
use App\Models\User;

class SparePartsUsage extends Model
{
    use HasFactory;

    protected $table = 'spare_parts_usage';

    protected $fillable = [
        'maintenance_activity_id',
        'spare_part_id',
        'equipment_id',
        'quantity_used',
        'unit_cost',
        'total_cost',
        'usage_reason',
        'condition_before',
        'condition_after',
        'installation_date',
        'warranty_period_months',
        'installed_by',
        'remarks',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'installation_date' => 'date',
    ];

    /**
     * Get the maintenance activity that used this spare part.
     */
    public function maintenanceActivity()
    {
        return $this->belongsTo(MaintenanceActivity::class);
    }

    /**
     * Get the spare part that was used.
     */
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    /**
     * Get the equipment where the spare part was installed.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who installed the spare part.
     */
    public function installedBy()
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    /**
     * Scope a query to only include usage for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereHas('maintenanceActivity', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('activity_date', [$startDate, $endDate]);
        });
    }

    /**
     * Scope a query to only include usage by reason.
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('usage_reason', $reason);
    }

    /**
     * Get the warranty expiry date.
     */
    public function getWarrantyExpiryAttribute()
    {
        if ($this->installation_date && $this->warranty_period_months) {
            return $this->installation_date->addMonths($this->warranty_period_months);
        }
        
        return null;
    }

    /**
     * Check if the warranty is still valid.
     */
    public function getIsUnderWarrantyAttribute()
    {
        return $this->warranty_expiry && $this->warranty_expiry > now();
    }

    /**
     * Get the days remaining in warranty.
     */
    public function getWarrantyDaysRemainingAttribute()
    {
        if ($this->warranty_expiry && $this->is_under_warranty) {
            return now()->diffInDays($this->warranty_expiry);
        }
        
        return 0;
    }

    /**
     * Calculate total cost automatically before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total_cost = $model->quantity_used * $model->unit_cost;
        });
    }
}
<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\Master\Chemical;
use App\Models\Master\Equipment;
use App\Models\User;

class ChemicalOperation extends Model
{
    use HasFactory;

    protected $table = 'chemical_operations';

    protected $fillable = [
        'vessel_id',
        'chemical_id',
        'equipment_id',
        'operation_date',
        'operation_time',
        'operation_type',
        'quantity_used_liters',
        'quantity_used_kg',
        'concentration_percentage',
        'injection_rate_lph',
        'injection_pressure_bar',
        'target_system',
        'purpose',
        'dosage_rate',
        'batch_number',
        'expiry_date',
        'pre_operation_reading',
        'post_operation_reading',
        'effectiveness_rating',
        'environmental_conditions',
        'safety_precautions_taken',
        'recorded_by',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'operation_time' => 'datetime',
        'quantity_used_liters' => 'decimal:2',
        'quantity_used_kg' => 'decimal:2',
        'concentration_percentage' => 'decimal:2',
        'injection_rate_lph' => 'decimal:2',
        'injection_pressure_bar' => 'decimal:2',
        'dosage_rate' => 'decimal:4',
        'expiry_date' => 'date',
        'pre_operation_reading' => 'decimal:2',
        'post_operation_reading' => 'decimal:2',
        'effectiveness_rating' => 'integer',
        'environmental_conditions' => 'array',
        'safety_precautions_taken' => 'array',
    ];

    /**
     * Get the vessel where the chemical operation was performed.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the chemical used in this operation.
     */
    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    /**
     * Get the equipment used for this chemical operation.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who recorded this operation.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the user who approved this operation.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include operations for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('operation_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include operations by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('operation_type', $type);
    }

    /**
     * Scope a query to only include operations by target system.
     */
    public function scopeByTargetSystem($query, $system)
    {
        return $query->where('target_system', $system);
    }

    /**
     * Scope a query to only include operations with high effectiveness.
     */
    public function scopeHighEffectiveness($query, $minRating = 4)
    {
        return $query->where('effectiveness_rating', '>=', $minRating);
    }

    /**
     * Scope a query to only include operations using expired chemicals.
     */
    public function scopeWithExpiredChemicals($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Get the operation effectiveness status.
     */
    public function getEffectivenessStatusAttribute()
    {
        if (!$this->effectiveness_rating) {
            return 'Not Rated';
        }

        if ($this->effectiveness_rating >= 4) {
            return 'Excellent';
        } elseif ($this->effectiveness_rating >= 3) {
            return 'Good';
        } elseif ($this->effectiveness_rating >= 2) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }

    /**
     * Get the chemical expiry status.
     */
    public function getChemicalExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'No Expiry Date';
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0) {
            return 'Expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'Expiring Soon';
        } else {
            return 'Valid';
        }
    }

    /**
     * Calculate the operation efficiency based on readings.
     */
    public function getOperationEfficiencyAttribute()
    {
        if ($this->pre_operation_reading && $this->post_operation_reading) {
            $improvement = $this->post_operation_reading - $this->pre_operation_reading;
            return round(($improvement / $this->pre_operation_reading) * 100, 2);
        }

        return null;
    }

    /**
     * Get the total chemical cost for this operation.
     */
    public function getOperationCostAttribute()
    {
        if ($this->chemical && $this->quantity_used_liters) {
            return $this->quantity_used_liters * $this->chemical->unit_price;
        }

        return 0;
    }

    /**
     * Check if the operation used hazardous chemicals.
     */
    public function getIsHazardousOperationAttribute()
    {
        return $this->chemical && $this->chemical->is_hazardous;
    }
}
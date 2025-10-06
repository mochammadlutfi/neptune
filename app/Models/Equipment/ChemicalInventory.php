<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\Master\Chemical;
use App\Models\User;

class ChemicalInventory extends Model
{
    use HasFactory;

    protected $table = 'chemical_inventory';

    protected $fillable = [
        'vessel_id',
        'chemical_id',
        'inventory_date',
        'quantity_onboard_liters',
        'quantity_onboard_kg',
        'quantity_received_liters',
        'quantity_received_kg',
        'quantity_used_liters',
        'quantity_used_kg',
        'concentration_percentage',
        'density_kgm3',
        'temperature_c',
        'ph_value',
        'viscosity_cst',
        'storage_location',
        'storage_conditions',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'supplier_name',
        'delivery_note_number',
        'certificate_of_analysis',
        'unit_price_per_liter',
        'unit_price_per_kg',
        'total_cost',
        'currency',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'safety_stock',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'quantity_onboard_liters' => 'decimal:2',
        'quantity_onboard_kg' => 'decimal:2',
        'quantity_received_liters' => 'decimal:2',
        'quantity_received_kg' => 'decimal:2',
        'quantity_used_liters' => 'decimal:2',
        'quantity_used_kg' => 'decimal:2',
        'concentration_percentage' => 'decimal:2',
        'density_kgm3' => 'decimal:2',
        'temperature_c' => 'decimal:2',
        'ph_value' => 'decimal:2',
        'viscosity_cst' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'unit_price_per_liter' => 'decimal:4',
        'unit_price_per_kg' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'min_stock_level' => 'decimal:2',
        'max_stock_level' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'safety_stock' => 'decimal:2',
        'storage_conditions' => 'array',
    ];

    /**
     * Get the vessel that owns the chemical inventory.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the chemical for this inventory record.
     */
    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    /**
     * Get the user who recorded this inventory.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope a query to only include inventory for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('inventory_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include inventory below reorder point.
     */
    public function scopeBelowReorderPoint($query)
    {
        return $query->whereRaw('quantity_onboard_liters <= reorder_point');
    }

    /**
     * Scope a query to only include inventory expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->whereNotNull('expiry_date');
    }

    /**
     * Scope a query to only include hazardous chemical inventory.
     */
    public function scopeHazardous($query)
    {
        return $query->whereHas('chemical', function ($q) {
            $q->where('is_hazardous', true);
        });
    }

    /**
     * Scope a query to only include inventory with receipts.
     */
    public function scopeWithReceipts($query)
    {
        return $query->where('quantity_received_liters', '>', 0);
    }

    /**
     * Get the stock status based on current quantity vs reorder point.
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity_onboard_liters <= 0) {
            return 'Out of Stock';
        } elseif ($this->quantity_onboard_liters <= $this->reorder_point) {
            return 'Reorder Required';
        } elseif ($this->quantity_onboard_liters <= $this->min_stock_level) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get the days until expiry.
     */
    public function getDaysToExpiryAttribute()
    {
        if ($this->expiry_date) {
            return now()->diffInDays($this->expiry_date, false);
        }
        
        return null;
    }

    /**
     * Check if the chemical is expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Get the chemical age in days.
     */
    public function getChemicalAgeAttribute()
    {
        if ($this->manufacturing_date) {
            return now()->diffInDays($this->manufacturing_date);
        }
        
        return null;
    }

    /**
     * Calculate the inventory value in liters.
     */
    public function getInventoryValueLitersAttribute()
    {
        return $this->quantity_onboard_liters * ($this->unit_price_per_liter ?? 0);
    }

    /**
     * Calculate the inventory value in kilograms.
     */
    public function getInventoryValueKgAttribute()
    {
        return $this->quantity_onboard_kg * ($this->unit_price_per_kg ?? 0);
    }

    /**
     * Get the total inventory value (prioritizing liters over kg).
     */
    public function getTotalInventoryValueAttribute()
    {
        if ($this->unit_price_per_liter && $this->quantity_onboard_liters) {
            return $this->inventory_value_liters;
        } elseif ($this->unit_price_per_kg && $this->quantity_onboard_kg) {
            return $this->inventory_value_kg;
        }
        
        return 0;
    }

    /**
     * Get the inventory turnover rate.
     */
    public function getInventoryTurnoverAttribute()
    {
        if ($this->quantity_onboard_liters > 0 && $this->quantity_used_liters > 0) {
            return round($this->quantity_used_liters / $this->quantity_onboard_liters, 2);
        }
        
        return 0;
    }

    /**
     * Check if storage conditions are within acceptable range.
     */
    public function getStorageConditionStatusAttribute()
    {
        $issues = [];
        
        // Check temperature if specified in chemical requirements
        if ($this->chemical && $this->chemical->storage_requirements) {
            $requirements = $this->chemical->storage_requirements;
            
            if (isset($requirements['max_temperature']) && $this->temperature_c > $requirements['max_temperature']) {
                $issues[] = 'Temperature too high';
            }
            
            if (isset($requirements['min_temperature']) && $this->temperature_c < $requirements['min_temperature']) {
                $issues[] = 'Temperature too low';
            }
        }
        
        return empty($issues) ? 'Good' : implode(', ', $issues);
    }
}
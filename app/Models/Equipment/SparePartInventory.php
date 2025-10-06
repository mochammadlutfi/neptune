<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class SparePartInventory extends Model
{
    use HasFactory;

    protected $table = 'spare_parts_inventory';

    protected $fillable = [
        'vessel_id',
        'spare_part_id',
        'inventory_date',
        'quantity_onboard',
        'quantity_received',
        'quantity_used',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'condition_status',
        'location_onboard',
        'expiry_date',
        'batch_number',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'expiry_date' => 'date',
        'quantity_onboard' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_used' => 'decimal:2',
        'min_stock_level' => 'decimal:2',
        'max_stock_level' => 'decimal:2',
        'reorder_point' => 'decimal:2',
    ];

    /**
     * Get the vessel that owns the inventory.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the spare part for this inventory record.
     */
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
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
        return $query->whereRaw('quantity_onboard <= reorder_point');
    }

    /**
     * Scope a query to only include inventory by condition status.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition_status', $condition);
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
     * Get the stock status based on current quantity vs reorder point.
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity_onboard <= 0) {
            return 'Out of Stock';
        } elseif ($this->quantity_onboard <= $this->reorder_point) {
            return 'Reorder Required';
        } elseif ($this->quantity_onboard <= $this->min_stock_level) {
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
     * Check if the inventory is expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Calculate the inventory value.
     */
    public function getInventoryValueAttribute()
    {
        return $this->quantity_onboard * ($this->sparePart->unit_price ?? 0);
    }
}
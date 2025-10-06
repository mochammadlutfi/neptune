<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePartsInventory extends Model
{
    use HasFactory;

    protected $table = 'spare_parts_inventory';

    protected $fillable = [
        'vessel_id',
        'spare_part_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_available',
        'unit_cost',
        'total_value',
        'last_updated',
        'location',
        'condition',
        'expiry_date',
        'batch_number',
        'supplier',
        'purchase_date',
        'warranty_expiry',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'quantity_available' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'last_updated' => 'datetime',
        'expiry_date' => 'date',
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->quantity_available = $model->quantity_on_hand - $model->quantity_reserved;
            $model->total_value = $model->quantity_on_hand * $model->unit_cost;
        });
    }

    public function vessel()
    {
        return $this->belongsTo(\App\Models\Master\Vessel::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SpareParts::class, 'spare_part_id');
    }

    /**
     * Scope a query to only include low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('sparePart', function ($q) {
            $q->whereRaw('spare_parts_inventory.quantity_on_hand <= spare_parts.minimum_stock');
        });
    }

    /**
     * Scope a query to only include items by condition.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Scope a query to only include items expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->whereNotNull('expiry_date');
    }

    /**
     * Get the stock status.
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity_available <= 0) {
            return 'Out of Stock';
        }
        
        if ($this->quantity_available <= $this->sparePart->minimum_stock) {
            return 'Low Stock';
        }
        
        return 'In Stock';
    }

    /**
     * Get the days until expiry.
     */
    public function getDaysToExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if the item is expired.
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date < now();
    }

    /**
     * Get the warranty status.
     */
    public function getWarrantyStatusAttribute()
    {
        if (!$this->warranty_expiry) {
            return 'No Warranty';
        }
        
        if ($this->warranty_expiry < now()) {
            return 'Expired';
        }
        
        $daysLeft = now()->diffInDays($this->warranty_expiry);
        
        if ($daysLeft <= 30) {
            return 'Expiring Soon';
        }
        
        return 'Active';
    }
}
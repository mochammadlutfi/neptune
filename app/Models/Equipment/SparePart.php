<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Equipment;

class SparePart extends Model
{
    use HasFactory;

    protected $table = 'spare_parts';

    protected $fillable = [
        'part_number',
        'part_name',
        'description',
        'category',
        'equipment_compatibility',
        'manufacturer',
        'supplier_name',
        'supplier_contact',
        'unit_price',
        'currency',
        'lead_time_days',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'is_critical',
        'storage_location',
        'shelf_life_months',
        'specifications',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_critical' => 'boolean',
        'specifications' => 'array',
    ];

    /**
     * Get the equipment that can use this spare part.
     */
    public function compatibleEquipment()
    {
        return $this->belongsToMany(Equipment::class, 'equipment_spare_parts', 'spare_part_id', 'equipment_id');
    }

    /**
     * Get the inventory records for this spare part.
     */
    public function inventory()
    {
        return $this->hasMany(SparePartInventory::class);
    }

    /**
     * Get the usage records for this spare part.
     */
    public function usage()
    {
        return $this->hasMany(SparePartsUsage::class);
    }

    /**
     * Scope a query to only include critical spare parts.
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Scope a query to only include parts by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include parts compatible with specific equipment.
     */
    public function scopeCompatibleWith($query, $equipmentId)
    {
        return $query->whereHas('compatibleEquipment', function ($q) use ($equipmentId) {
            $q->where('equipment.id', $equipmentId);
        });
    }

    /**
     * Get the current total stock across all vessels.
     */
    public function getCurrentStockAttribute()
    {
        return $this->inventory()
            ->selectRaw('SUM(quantity_onboard) as total')
            ->value('total') ?: 0;
    }

    /**
     * Get the stock status based on current stock vs reorder point.
     */
    public function getStockStatusAttribute()
    {
        $currentStock = $this->current_stock;
        
        if ($currentStock <= 0) {
            return 'Out of Stock';
        } elseif ($currentStock <= $this->reorder_point) {
            return 'Reorder Required';
        } elseif ($currentStock <= $this->minimum_stock) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Calculate total value of current stock.
     */
    public function getStockValueAttribute()
    {
        return $this->current_stock * $this->unit_price;
    }
}
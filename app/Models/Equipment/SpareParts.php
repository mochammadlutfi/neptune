<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpareParts extends Model
{
    use HasFactory;

    protected $table = 'spare_parts';

    protected $fillable = [
        'part_number',
        'part_name',
        'description',
        'category',
        'manufacturer',
        'unit_price',
        'currency',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'lead_time_days',
        'storage_location',
        'is_critical',
        'specifications',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'reorder_point' => 'integer',
        'lead_time_days' => 'integer',
        'is_critical' => 'boolean',
        'specifications' => 'array',
    ];

    public function inventory()
    {
        return $this->hasMany(SparePartsInventory::class, 'spare_part_id');
    }

    public function equipment()
    {
        return $this->belongsToMany(\App\Models\Master\Equipment::class, 'equipment_spare_parts', 'spare_part_id', 'equipment_id');
    }

    /**
     * Scope a query to only include critical spare parts.
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Scope a query to only include spare parts by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include spare parts needing reorder.
     */
    public function scopeNeedingReorder($query)
    {
        return $query->whereHas('inventory', function ($q) {
            $q->selectRaw('spare_part_id, SUM(quantity_on_hand) as total_stock')
              ->groupBy('spare_part_id')
              ->havingRaw('total_stock <= reorder_point');
        });
    }

    /**
     * Get the current total stock across all vessels.
     */
    public function getCurrentStockAttribute()
    {
        return $this->inventory()->sum('quantity_on_hand');
    }

    /**
     * Get the stock status.
     */
    public function getStockStatusAttribute()
    {
        $currentStock = $this->current_stock;
        
        if ($currentStock <= $this->reorder_point) {
            return 'Reorder Required';
        }
        
        if ($currentStock <= $this->minimum_stock) {
            return 'Low Stock';
        }
        
        if ($currentStock >= $this->maximum_stock) {
            return 'Overstock';
        }
        
        return 'Normal';
    }

    /**
     * Get the total inventory value.
     */
    public function getInventoryValueAttribute()
    {
        return $this->current_stock * $this->unit_price;
    }
}
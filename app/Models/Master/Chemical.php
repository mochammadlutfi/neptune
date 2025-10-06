<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $table = 'chemicals';

    protected $fillable = [
        'chemical_name',
        'chemical_type',
        'cas_number',
        'chemical_formula',
        'description',
        'manufacturer',
        'supplier_name',
        'supplier_contact',
        'unit_of_measure',
        'unit_price',
        'currency',
        'density_kgm3',
        'flash_point_c',
        'boiling_point_c',
        'ph_value',
        'viscosity_cst',
        'hazard_classification',
        'safety_data_sheet_url',
        'storage_requirements',
        'shelf_life_months',
        'minimum_stock_level',
        'maximum_stock_level',
        'reorder_point',
        'is_hazardous',
        'disposal_method',
        'environmental_impact',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'density_kgm3' => 'decimal:2',
        'flash_point_c' => 'decimal:2',
        'boiling_point_c' => 'decimal:2',
        'ph_value' => 'decimal:2',
        'viscosity_cst' => 'decimal:2',
        'minimum_stock_level' => 'decimal:2',
        'maximum_stock_level' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'is_hazardous' => 'boolean',
        'storage_requirements' => 'array',
    ];

    /**
     * Get the chemical operations that use this chemical.
     */
    public function operations()
    {
        return $this->hasMany(\App\Models\Equipment\ChemicalOperation::class);
    }

    /**
     * Get the inventory records for this chemical.
     */
    public function inventory()
    {
        return $this->hasMany(\App\Models\Equipment\ChemicalInventory::class);
    }

    /**
     * Scope a query to only include hazardous chemicals.
     */
    public function scopeHazardous($query)
    {
        return $query->where('is_hazardous', true);
    }

    /**
     * Scope a query to only include chemicals by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('chemical_type', $type);
    }

    /**
     * Scope a query to only include chemicals needing reorder.
     */
    public function scopeNeedingReorder($query)
    {
        return $query->whereHas('inventory', function ($q) {
            $q->selectRaw('chemical_id, SUM(quantity_onboard) as total_stock')
              ->groupBy('chemical_id')
              ->havingRaw('total_stock <= chemicals.reorder_point');
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
        } elseif ($currentStock <= $this->minimum_stock_level) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get the safety classification display.
     */
    public function getSafetyStatusAttribute()
    {
        return $this->is_hazardous ? 'Hazardous' : 'Non-Hazardous';
    }
}

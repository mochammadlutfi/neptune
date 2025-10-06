<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Vessel;
use App\Models\User;

class FuelInventory extends Model
{
    use HasFactory;

    protected $table = 'fuel_inventory';

    protected $fillable = [
        'vessel_id',
        'fuel_tank_id',
        'inventory_date',
        'fuel_type',
        'opening_stock_liters',
        'received_liters',
        'consumed_liters',
        'closing_stock_liters',
        'density_kgm3',
        'temperature_c',
        'water_content_ppm',
        'sulfur_content_ppm',
        'viscosity_cst',
        'flash_point_c',
        'supplier_name',
        'delivery_note_number',
        'bunker_delivery_receipt',
        'fuel_quality_certificate',
        'unit_price_per_liter',
        'total_cost',
        'currency',
        'recorded_by',
        'remarks',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'opening_stock_liters' => 'decimal:2',
        'received_liters' => 'decimal:2',
        'consumed_liters' => 'decimal:2',
        'closing_stock_liters' => 'decimal:2',
        'density_kgm3' => 'decimal:2',
        'temperature_c' => 'decimal:2',
        'water_content_ppm' => 'decimal:2',
        'sulfur_content_ppm' => 'decimal:2',
        'viscosity_cst' => 'decimal:2',
        'flash_point_c' => 'decimal:2',
        'unit_price_per_liter' => 'decimal:4',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the vessel that owns the fuel inventory.
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the fuel tank for this inventory record.
     */
    public function fuelTank()
    {
        return $this->belongsTo(FuelTank::class);
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
     * Scope a query to only include inventory by fuel type.
     */
    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Scope a query to only include inventory with fuel receipts.
     */
    public function scopeWithReceipts($query)
    {
        return $query->where('received_liters', '>', 0);
    }

    /**
     * Scope a query to only include inventory with consumption.
     */
    public function scopeWithConsumption($query)
    {
        return $query->where('consumed_liters', '>', 0);
    }

    /**
     * Get the fuel mass in kilograms for opening stock.
     */
    public function getOpeningStockKgAttribute()
    {
        return $this->opening_stock_liters * ($this->density_kgm3 / 1000);
    }

    /**
     * Get the fuel mass in kilograms for closing stock.
     */
    public function getClosingStockKgAttribute()
    {
        return $this->closing_stock_liters * ($this->density_kgm3 / 1000);
    }

    /**
     * Get the fuel mass in kilograms for received fuel.
     */
    public function getReceivedKgAttribute()
    {
        return $this->received_liters * ($this->density_kgm3 / 1000);
    }

    /**
     * Get the fuel mass in kilograms for consumed fuel.
     */
    public function getConsumedKgAttribute()
    {
        return $this->consumed_liters * ($this->density_kgm3 / 1000);
    }

    /**
     * Get the inventory variance (difference between calculated and actual closing stock).
     */
    public function getInventoryVarianceAttribute()
    {
        $calculatedClosing = $this->opening_stock_liters + $this->received_liters - $this->consumed_liters;
        return $this->closing_stock_liters - $calculatedClosing;
    }

    /**
     * Get the fuel quality status based on specifications.
     */
    public function getFuelQualityStatusAttribute()
    {
        $issues = [];

        // Check water content (typically should be < 200 ppm for marine fuel)
        if ($this->water_content_ppm > 200) {
            $issues[] = 'High water content';
        }

        // Check sulfur content (IMO 2020 regulation: < 0.5% = 5000 ppm)
        if ($this->sulfur_content_ppm > 5000) {
            $issues[] = 'High sulfur content';
        }

        return empty($issues) ? 'Good' : implode(', ', $issues);
    }

    /**
     * Calculate total cost automatically before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->received_liters && $model->unit_price_per_liter) {
                $model->total_cost = $model->received_liters * $model->unit_price_per_liter;
            }
        });
    }
}
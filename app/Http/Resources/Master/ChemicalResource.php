<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChemicalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'type' => $this->type,
            'chemical_formula' => $this->chemical_formula,
            'molecular_weight' => $this->molecular_weight ? number_format($this->molecular_weight, 2) : null,
            'density_kg_l' => $this->density_kg_l ? number_format($this->density_kg_l, 3) : null,
            'viscosity_cp' => $this->viscosity_cp ? number_format($this->viscosity_cp, 2) : null,
            'ph_value' => $this->ph_value ? number_format($this->ph_value, 1) : null,
            'unit' => $this->unit,
            'package_size' => $this->package_size,
            'unit_package' => $this->unit && $this->package_size ? "{$this->unit} - {$this->package_size}" : ($this->unit ?: $this->package_size),
            'hazard_class' => $this->hazard_class,
            'un_number' => $this->un_number,
            'hazard_un' => $this->formatHazardClassification(),
            'safety_data_sheet_no' => $this->safety_data_sheet_no,
            'ppe_requirements' => $this->ppe_requirements,
            'storage_temperature_min_c' => $this->storage_temperature_min_c ? number_format($this->storage_temperature_min_c, 1) : null,
            'storage_temperature_max_c' => $this->storage_temperature_max_c ? number_format($this->storage_temperature_max_c, 1) : null,
            'storage_temperature_range' => $this->formatTemperatureRange(),
            'shelf_life_months' => $this->shelf_life_months,
            'storage_location' => $this->storage_location,
            'incompatible_chemicals' => $this->incompatible_chemicals,
            'primary_supplier' => $this->primary_supplier,
            'supplier_contact' => $this->supplier_contact,
            'lead_time_days' => $this->lead_time_days,
            'min_stock_level' => $this->min_stock_level ? number_format($this->min_stock_level, 2) : null,
            'max_stock_level' => $this->max_stock_level ? number_format($this->max_stock_level, 2) : null,
            'reorder_point' => $this->reorder_point ? number_format($this->reorder_point, 2) : null,
            'stock_status' => $this->formatStockStatus(),
            'is_active' => $this->is_active,
            'status_label' => $this->is_active ? 'Active' : 'Inactive',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatHazardClassification(): ?string
    {
        $parts = [];
        
        if ($this->hazard_class) {
            $parts[] = "Class {$this->hazard_class}";
        }
        
        if ($this->un_number) {
            $parts[] = "UN{$this->un_number}";
        }
        
        return !empty($parts) ? implode(' - ', $parts) : null;
    }

    private function formatTemperatureRange(): ?string
    {
        if ($this->storage_temperature_min_c && $this->storage_temperature_max_c) {
            return number_format($this->storage_temperature_min_c, 1) . '°C to ' . number_format($this->storage_temperature_max_c, 1) . '°C';
        } elseif ($this->storage_temperature_min_c) {
            return 'Min: ' . number_format($this->storage_temperature_min_c, 1) . '°C';
        } elseif ($this->storage_temperature_max_c) {
            return 'Max: ' . number_format($this->storage_temperature_max_c, 1) . '°C';
        }
        
        return null;
    }

    private function formatStockStatus(): string
    {
        $status = $this->is_active ? 'Active' : 'Inactive';
        
        // Mock current stock level for demonstration
        // In real implementation, this would come from inventory management
        $currentStock = 0; // This should be calculated from actual inventory
        $stockLevel = 'Unknown';
        
        if ($this->min_stock_level && $this->max_stock_level) {
            if ($currentStock <= $this->min_stock_level) {
                $stockLevel = 'Low';
            } elseif ($currentStock >= $this->max_stock_level) {
                $stockLevel = 'High';
            } else {
                $stockLevel = 'Normal';
            }
        }
        
        return "{$stockLevel} Stock - {$status}";
    }
}
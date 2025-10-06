<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SparePartsResource extends JsonResource
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
            'vessel_id' => $this->vessel_id,
            'vessel_name' => $this->vessel?->name,
            'part_number' => $this->part_number,
            'part_name' => $this->part_name,
            'part_description' => $this->part_description,
            'equipment_id' => $this->equipment_id,
            'equipment_name' => $this->equipment?->name,
            'equipment_tag' => $this->equipment?->tag,
            'equipment_code' => $this->equipment?->code,
            'part_category' => $this->part_category,
            'part_category_badge' => $this->getCategoryBadgeClass(),
            'manufacturer' => $this->manufacturer,
            'supplier' => $this->supplier,
            'unit_cost' => $this->unit_cost ? (float) $this->unit_cost : null,
            'unit_cost_formatted' => $this->getFormattedCost(),
            'currency' => $this->currency ?? 'USD',
            'unit_measure' => $this->unit_measure ?? 'Each',
            'min_stock_level' => $this->min_stock_level ? (int) $this->min_stock_level : 0,
            'reorder_point' => $this->reorder_point ? (int) $this->reorder_point : 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Calculated fields
            'full_part_name' => $this->getFullPartName(),
            'cost_estimate_display' => $this->getCostEstimateDisplay(),

            // Related models
            'vessel' => $this->whenLoaded('vessel'),
            'equipment' => $this->whenLoaded('equipment'),
        ];
    }

    /**
     * Get CSS class for category badge
     */
    private function getCategoryBadgeClass(): string
    {
        return match($this->part_category) {
            'Engine' => 'badge-danger',
            'Electrical' => 'badge-warning',
            'Mechanical' => 'badge-info',
            'Instrumentation' => 'badge-success',
            'Safety' => 'badge-primary',
            default => 'badge-secondary'
        };
    }

    /**
     * Get formatted cost with currency
     */
    private function getFormattedCost(): ?string
    {
        if (!$this->unit_cost) {
            return null;
        }

        $currency = $this->currency ?? 'USD';
        return $currency . ' ' . number_format($this->unit_cost, 2);
    }

    /**
     * Get full part name with part number
     */
    private function getFullPartName(): string
    {
        return "{$this->part_number} - {$this->part_name}";
    }

    /**
     * Get cost estimate display with stock levels
     */
    private function getCostEstimateDisplay(): ?string
    {
        if (!$this->unit_cost || !$this->min_stock_level) {
            return null;
        }

        $totalCost = $this->unit_cost * $this->min_stock_level;
        $currency = $this->currency ?? 'USD';

        return "{$currency} " . number_format($totalCost, 2) . " (min stock)";
    }
}
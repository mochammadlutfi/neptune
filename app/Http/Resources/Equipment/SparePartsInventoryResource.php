<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SparePartsInventoryResource extends JsonResource
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
            'spare_part_id' => $this->spare_part_id,
            'part_number' => $this->sparePart?->part_number,
            'part_name' => $this->sparePart?->part_name,
            'part_category' => $this->sparePart?->part_category,
            'equipment_name' => $this->sparePart?->equipment?->name,
            'equipment_tag' => $this->sparePart?->equipment?->tag,
            'vessel_name' => $this->sparePart?->vessel?->name,
            'inventory_date' => $this->inventory_date?->format('Y-m-d'),
            'quantity_onboard' => $this->quantity_onboard ? (int) $this->quantity_onboard : 0,
            'min_stock_level' => $this->min_stock_level ? (int) $this->min_stock_level : 0,
            'reorder_point' => $this->reorder_point ? (int) $this->reorder_point : 0,
            'reorder_quantity' => $this->reorder_quantity ? (int) $this->reorder_quantity : 0,
            'last_reorder_date' => $this->last_reorder_date?->format('Y-m-d'),
            'storage_location' => $this->storage_location,
            'condition_status' => $this->condition_status,
            'condition_badge_class' => $this->getConditionBadgeClass(),
            'remarks' => $this->remarks,
            'recorded_by' => $this->recorded_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Calculated fields
            'stock_status' => $this->getStockStatus(),
            'stock_status_badge' => $this->getStockStatusBadge(),
            'days_since_reorder' => $this->getDaysSinceReorder(),
            'quantity_needed' => $this->getQuantityNeeded(),
            'stock_level_percentage' => $this->getStockLevelPercentage(),

            // Related models
            'spare_part' => $this->whenLoaded('sparePart'),
        ];
    }

    /**
     * Get CSS class for condition badge
     */
    private function getConditionBadgeClass(): string
    {
        return match($this->condition_status) {
            'New' => 'badge-success',
            'Used' => 'badge-info',
            'Repair Required' => 'badge-warning',
            'Obsolete' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get stock status
     */
    private function getStockStatus(): string
    {
        if ($this->quantity_onboard <= 0) {
            return 'Out of Stock';
        } elseif ($this->quantity_onboard <= $this->min_stock_level) {
            return 'Critical Level';
        } elseif ($this->quantity_onboard <= $this->reorder_point) {
            return 'Reorder Required';
        } else {
            return 'Adequate';
        }
    }

    /**
     * Get stock status badge class
     */
    private function getStockStatusBadge(): string
    {
        $status = $this->getStockStatus();
        return match($status) {
            'Out of Stock' => 'badge-danger',
            'Critical Level' => 'badge-warning',
            'Reorder Required' => 'badge-info',
            'Adequate' => 'badge-success',
            default => 'badge-secondary'
        };
    }

    /**
     * Get days since last reorder
     */
    private function getDaysSinceReorder(): ?int
    {
        if (!$this->last_reorder_date) {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->last_reorder_date);
    }

    /**
     * Get quantity needed to reach reorder point
     */
    private function getQuantityNeeded(): int
    {
        if ($this->quantity_onboard >= $this->reorder_point) {
            return 0;
        }

        return $this->reorder_point - $this->quantity_onboard;
    }

    /**
     * Get stock level as percentage of reorder point
     */
    private function getStockLevelPercentage(): float
    {
        if ($this->reorder_point <= 0) {
            return 100;
        }

        return round(($this->quantity_onboard / $this->reorder_point) * 100, 1);
    }
}
<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelTanksResource extends JsonResource
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
            'tank_name' => $this->tank_name,
            'tank_number' => $this->tank_number,
            'capacity_liters' => $this->capacity_liters ? (float) $this->capacity_liters : null,
            'capacity_formatted' => $this->getFormattedCapacity(),
            'fuel_type' => $this->fuel_type,
            'fuel_type_badge' => $this->getFuelTypeBadgeClass(),
            'tank_location' => $this->tank_location,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Calculated fields
            'tank_display_name' => $this->getTankDisplayName(),
            'capacity_barrels' => $this->getCapacityInBarrels(),

            // Related models
            'vessel' => $this->whenLoaded('vessel'),
        ];
    }

    /**
     * Get CSS class for fuel type badge
     */
    private function getFuelTypeBadgeClass(): string
    {
        return match($this->fuel_type) {
            'Diesel' => 'badge-primary',
            'MGO' => 'badge-info',
            'HFO' => 'badge-warning',
            'Lube Oil' => 'badge-success',
            default => 'badge-secondary'
        };
    }

    /**
     * Get formatted capacity with units
     */
    private function getFormattedCapacity(): string
    {
        if (!$this->capacity_liters) {
            return 'N/A';
        }

        return number_format($this->capacity_liters, 0) . ' L';
    }

    /**
     * Get tank display name
     */
    private function getTankDisplayName(): string
    {
        return "{$this->tank_name} (Tank #{$this->tank_number})";
    }

    /**
     * Get capacity in barrels (1 barrel = 158.987 liters)
     */
    private function getCapacityInBarrels(): ?float
    {
        if (!$this->capacity_liters) {
            return null;
        }

        return round($this->capacity_liters / 158.987, 2);
    }
}
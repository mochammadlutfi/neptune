<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelInventoryResource extends JsonResource
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
            'tank_id' => $this->tank_id,
            'tank_name' => $this->tank?->tank_name,
            'tank_number' => $this->tank?->tank_number,
            'tank_capacity' => $this->tank?->capacity_liters,
            'fuel_type' => $this->tank?->fuel_type,
            'inventory_date' => $this->inventory_date?->format('Y-m-d'),
            'opening_volume_liters' => $this->opening_volume_liters ? (float) $this->opening_volume_liters : null,
            'received_volume_liters' => $this->received_volume_liters ? (float) $this->received_volume_liters : null,
            'consumed_volume_liters' => $this->consumed_volume_liters ? (float) $this->consumed_volume_liters : null,
            'rob_volume_liters' => $this->rob_volume_liters ? (float) $this->rob_volume_liters : 0,
            'ullage_level' => $this->ullage_level ? (float) $this->ullage_level : null,
            'temperature' => $this->temperature ? (float) $this->temperature : null,
            'density' => $this->density ? (float) $this->density : null,
            'water_content' => $this->water_content ? (float) $this->water_content : null,
            'remarks' => $this->remarks,
            'recorded_by' => $this->recorded_by,
            'recorded_by_name' => $this->recordedBy?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Calculated fields
            'fill_percentage' => $this->getFillPercentage(),
            'fill_status' => $this->getFillStatus(),
            'fill_status_badge' => $this->getFillStatusBadge(),
            'rob_formatted' => $this->getFormattedROB(),
            'material_balance' => $this->getMaterialBalance(),
            'balance_variance' => $this->getBalanceVariance(),
            'rob_barrels' => $this->getROBInBarrels(),
            'consumption_rate' => $this->getConsumptionRate(),

            // Related models
            'vessel' => $this->whenLoaded('vessel'),
            'tank' => $this->whenLoaded('tank'),
            'recordedBy' => $this->whenLoaded('recordedBy'),
        ];
    }

    /**
     * Get fill percentage of tank
     */
    private function getFillPercentage(): float
    {
        if (!$this->tank?->capacity_liters || $this->tank->capacity_liters <= 0) {
            return 0;
        }

        return round(($this->rob_volume_liters / $this->tank->capacity_liters) * 100, 1);
    }

    /**
     * Get fill status based on percentage
     */
    private function getFillStatus(): string
    {
        $percentage = $this->getFillPercentage();

        if ($percentage <= 5) {
            return 'Critical';
        } elseif ($percentage <= 15) {
            return 'Low';
        } elseif ($percentage <= 35) {
            return 'Moderate';
        } elseif ($percentage <= 85) {
            return 'Good';
        } else {
            return 'Full';
        }
    }

    /**
     * Get fill status badge class
     */
    private function getFillStatusBadge(): string
    {
        $status = $this->getFillStatus();
        return match($status) {
            'Critical' => 'badge-danger',
            'Low' => 'badge-warning',
            'Moderate' => 'badge-info',
            'Good' => 'badge-success',
            'Full' => 'badge-primary',
            default => 'badge-secondary'
        };
    }

    /**
     * Get formatted ROB with units
     */
    private function getFormattedROB(): string
    {
        return number_format($this->rob_volume_liters, 2) . ' L';
    }

    /**
     * Calculate material balance
     */
    private function getMaterialBalance(): float
    {
        $opening = $this->opening_volume_liters ?? 0;
        $received = $this->received_volume_liters ?? 0;
        $consumed = $this->consumed_volume_liters ?? 0;

        return round($opening + $received - $consumed, 2);
    }

    /**
     * Get balance variance percentage
     */
    private function getBalanceVariance(): float
    {
        $materialBalance = $this->getMaterialBalance();

        if ($materialBalance == 0) {
            return 0;
        }

        $variance = (($this->rob_volume_liters - $materialBalance) / $materialBalance) * 100;
        return round($variance, 2);
    }

    /**
     * Get ROB in barrels (1 barrel = 158.987 liters)
     */
    private function getROBInBarrels(): float
    {
        return round($this->rob_volume_liters / 158.987, 2);
    }

    /**
     * Get consumption rate if previous day data available
     */
    private function getConsumptionRate(): ?float
    {
        if (!$this->consumed_volume_liters) {
            return null;
        }

        // Return consumption per day
        return round($this->consumed_volume_liters, 2);
    }
}
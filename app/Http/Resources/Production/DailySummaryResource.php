<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailySummaryResource extends JsonResource
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
            'vessel' => $this->whenLoaded('vessel', function () {
                return [
                    'id' => $this->vessel->id,
                    'name' => $this->vessel->name,
                    'code' => $this->vessel->code ?? null,
                ];
            }),
            'summary_date' => $this->summary_date?->format('Y-m-d'),
            'production_summary' => [
                'total_oil_bbl' => $this->total_oil_bbl ? (float) $this->total_oil_bbl : 0,
                'total_gas_mmscf' => $this->total_gas_mmscf ? (float) $this->total_gas_mmscf : 0,
                'total_water_bbl' => $this->total_water_bbl ? (float) $this->total_water_bbl : 0,
                'water_cut_pct' => $this->water_cut_pct ? (float) $this->water_cut_pct : 0,
                'total_liquid_bbl' => $this->total_oil_bbl && $this->total_water_bbl 
                    ? (float) ($this->total_oil_bbl + $this->total_water_bbl) 
                    : 0,
                'oil_percentage' => $this->total_oil_bbl && $this->total_water_bbl && ($this->total_oil_bbl + $this->total_water_bbl) > 0
                    ? round(($this->total_oil_bbl / ($this->total_oil_bbl + $this->total_water_bbl)) * 100, 2)
                    : 0,
            ],
            'gas_utilization' => [
                'gas_export_mmscf' => $this->gas_export_mmscf ? (float) $this->gas_export_mmscf : 0,
                'gas_fuel_mmscf' => $this->gas_fuel_mmscf ? (float) $this->gas_fuel_mmscf : 0,
                'gas_flare_mmscf' => $this->gas_flare_mmscf ? (float) $this->gas_flare_mmscf : 0,
                'total_gas_utilized_mmscf' => $this->gas_export_mmscf && $this->gas_fuel_mmscf && $this->gas_flare_mmscf
                    ? (float) ($this->gas_export_mmscf + $this->gas_fuel_mmscf + $this->gas_flare_mmscf)
                    : 0,
                'export_percentage' => $this->total_gas_mmscf && $this->gas_export_mmscf && $this->total_gas_mmscf > 0
                    ? round(($this->gas_export_mmscf / $this->total_gas_mmscf) * 100, 2)
                    : 0,
                'fuel_percentage' => $this->total_gas_mmscf && $this->gas_fuel_mmscf && $this->total_gas_mmscf > 0
                    ? round(($this->gas_fuel_mmscf / $this->total_gas_mmscf) * 100, 2)
                    : 0,
                'flare_percentage' => $this->total_gas_mmscf && $this->gas_flare_mmscf && $this->total_gas_mmscf > 0
                    ? round(($this->gas_flare_mmscf / $this->total_gas_mmscf) * 100, 2)
                    : 0,
            ],
            'equipment_performance' => [
                'total_equipment' => (int) ($this->total_equipment ?? 0),
                'equipment_running' => (int) ($this->equipment_running ?? 0),
                'equipment_availability_pct' => $this->equipment_availability_pct ? (float) $this->equipment_availability_pct : 0,
                'equipment_downtime_pct' => $this->equipment_availability_pct 
                    ? (float) (100 - $this->equipment_availability_pct) 
                    : 0,
                'performance_status' => $this->getPerformanceStatus(),
            ],
            'personnel_safety' => [
                'total_pob' => (int) ($this->total_pob ?? 0),
                'safety_incidents' => (int) ($this->safety_incidents ?? 0),
                'safety_performance' => $this->safety_incidents == 0 ? 'Excellent' : ($this->safety_incidents <= 2 ? 'Good' : 'Needs Attention'),
            ],
            'efficiency_metrics' => [
                'oil_per_well_bbl' => $this->getOilPerWell(),
                'gas_per_well_mmscf' => $this->getGasPerWell(),
                'uptime_efficiency_pct' => $this->equipment_availability_pct ? (float) $this->equipment_availability_pct : 0,
                'production_efficiency_rating' => $this->getProductionEfficiencyRating(),
            ],
            'calculation_info' => [
                'calculation_status' => $this->calculation_status,
                'calculated_at' => $this->calculated_at?->format('Y-m-d H:i:s'),
                'is_manual' => $this->calculation_status === 'manual',
                'is_calculated' => in_array($this->calculation_status, ['calculated', 'recalculated']),
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get performance status based on equipment availability
     */
    private function getPerformanceStatus(): string
    {
        $availability = $this->equipment_availability_pct ?? 0;
        
        if ($availability >= 95) {
            return 'Excellent';
        } elseif ($availability >= 85) {
            return 'Good';
        } elseif ($availability >= 70) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }

    /**
     * Calculate oil production per well (assuming average number of wells)
     */
    private function getOilPerWell(): ?float
    {
        // This would need to be calculated based on actual well count
        // For now, assuming average of 10 wells per vessel
        $averageWells = 10;
        
        if ($this->total_oil_bbl && $averageWells > 0) {
            return round($this->total_oil_bbl / $averageWells, 2);
        }
        
        return null;
    }

    /**
     * Calculate gas production per well (assuming average number of wells)
     */
    private function getGasPerWell(): ?float
    {
        // This would need to be calculated based on actual well count
        // For now, assuming average of 10 wells per vessel
        $averageWells = 10;
        
        if ($this->total_gas_mmscf && $averageWells > 0) {
            return round($this->total_gas_mmscf / $averageWells, 2);
        }
        
        return null;
    }

    /**
     * Get overall production efficiency rating
     */
    private function getProductionEfficiencyRating(): string
    {
        $oilProduction = $this->total_oil_bbl ?? 0;
        $gasProduction = $this->total_gas_mmscf ?? 0;
        $equipmentAvailability = $this->equipment_availability_pct ?? 0;
        $safetyIncidents = $this->safety_incidents ?? 0;
        
        // Simple scoring system (this would be more sophisticated in real implementation)
        $score = 0;
        
        // Production volume score (40% weight)
        if ($oilProduction > 1000) $score += 40;
        elseif ($oilProduction > 500) $score += 30;
        elseif ($oilProduction > 100) $score += 20;
        else $score += 10;
        
        // Equipment availability score (40% weight)
        if ($equipmentAvailability >= 95) $score += 40;
        elseif ($equipmentAvailability >= 85) $score += 30;
        elseif ($equipmentAvailability >= 70) $score += 20;
        else $score += 10;
        
        // Safety score (20% weight)
        if ($safetyIncidents == 0) $score += 20;
        elseif ($safetyIncidents <= 1) $score += 15;
        elseif ($safetyIncidents <= 2) $score += 10;
        else $score += 5;
        
        if ($score >= 90) return 'Excellent';
        elseif ($score >= 75) return 'Good';
        elseif ($score >= 60) return 'Fair';
        else return 'Poor';
    }
}
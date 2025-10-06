<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FPUOperationsResource extends JsonResource
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
            'reading_date' => $this->reading_date?->format('Y-m-d'),
            'reading_hour' => (int) $this->reading_hour,
            'reading_datetime' => $this->reading_date?->format('Y-m-d') . ' ' . str_pad($this->reading_hour, 2, '0', STR_PAD_LEFT) . ':00:00',
            'inlet_pressure_psi' => $this->inlet_pressure_psi ? (float) $this->inlet_pressure_psi : null,
            'inlet_temp_f' => $this->inlet_temp_f ? (float) $this->inlet_temp_f : null,
            'outlet_pressure_psi' => $this->outlet_pressure_psi ? (float) $this->outlet_pressure_psi : null,
            'outlet_temp_f' => $this->outlet_temp_f ? (float) $this->outlet_temp_f : null,
            'pressure_differential_psi' => $this->inlet_pressure_psi && $this->outlet_pressure_psi 
                ? (float) ($this->inlet_pressure_psi - $this->outlet_pressure_psi) 
                : null,
            'temp_differential_f' => $this->inlet_temp_f && $this->outlet_temp_f 
                ? (float) ($this->outlet_temp_f - $this->inlet_temp_f) 
                : null,
            'total_gas_rate_mmscfd' => $this->total_gas_rate_mmscfd ? (float) $this->total_gas_rate_mmscfd : null,
            'fuel_gas_rate_mmscfd' => $this->fuel_gas_rate_mmscfd ? (float) $this->fuel_gas_rate_mmscfd : null,
            'flare_hp_rate_mmscfd' => $this->flare_hp_rate_mmscfd ? (float) $this->flare_hp_rate_mmscfd : null,
            'flare_lp_rate_mmscfd' => $this->flare_lp_rate_mmscfd ? (float) $this->flare_lp_rate_mmscfd : null,
            'total_flare_rate_mmscfd' => $this->flare_hp_rate_mmscfd && $this->flare_lp_rate_mmscfd 
                ? (float) ($this->flare_hp_rate_mmscfd + $this->flare_lp_rate_mmscfd) 
                : null,
            'net_gas_rate_mmscfd' => $this->total_gas_rate_mmscfd && $this->fuel_gas_rate_mmscfd && $this->flare_hp_rate_mmscfd && $this->flare_lp_rate_mmscfd
                ? (float) ($this->total_gas_rate_mmscfd - $this->fuel_gas_rate_mmscfd - $this->flare_hp_rate_mmscfd - $this->flare_lp_rate_mmscfd)
                : null,
            'fuel_gas_percentage' => $this->total_gas_rate_mmscfd && $this->fuel_gas_rate_mmscfd && $this->total_gas_rate_mmscfd > 0
                ? round(($this->fuel_gas_rate_mmscfd / $this->total_gas_rate_mmscfd) * 100, 2)
                : null,
            'flare_percentage' => $this->total_gas_rate_mmscfd && $this->flare_hp_rate_mmscfd && $this->flare_lp_rate_mmscfd && $this->total_gas_rate_mmscfd > 0
                ? round((($this->flare_hp_rate_mmscfd + $this->flare_lp_rate_mmscfd) / $this->total_gas_rate_mmscfd) * 100, 2)
                : null,
            'process_data' => $this->process_data ? json_decode($this->process_data, true) : null,
            'recorded_by' => $this->recorded_by,
            'recorded_by_user' => $this->whenLoaded('recordedBy', function () {
                return [
                    'id' => $this->recordedBy->id,
                    'name' => $this->recordedBy->name,
                    'email' => $this->recordedBy->email,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellProductionResource extends JsonResource
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
            'well_id' => $this->well_id,
            'well_name' => $this->well?->name,
            'well_code' => $this->well?->code,
            'reading_datetime' => $this->reading_datetime?->format('Y-m-d H:i:s'),
            'recorded_date' => $this->reading_datetime?->format('Y-m-d'),
            'recorded_time' => $this->reading_datetime?->format('H:i:s'),
            'shift' => $this->shift,
            'oil_rate_bph' => $this->oil_rate_bph ? (float) $this->oil_rate_bph : null,
            'gas_rate_mscfh' => $this->gas_rate_mscfh ? (float) $this->gas_rate_mscfh : null,
            'water_rate_bph' => $this->water_rate_bph ? (float) $this->water_rate_bph : null,
            'wellhead_pressure_psi' => $this->wellhead_pressure_psi ? (float) $this->wellhead_pressure_psi : null,
            'wellhead_temperature_f' => $this->wellhead_temperature_f ? (float) $this->wellhead_temperature_f : null,
            'choke_size_64th' => $this->choke_size_64th ? (int) $this->choke_size_64th : null,
            'flow_hours' => $this->flow_hours ? (float) $this->flow_hours : null,
            'api_gravity' => $this->api_gravity ? (float) $this->api_gravity : null,
            'bs_w_percent' => $this->bs_w_percent ? (float) $this->bs_w_percent : null,
            'recorded_by' => $this->recorded_by,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'well' => $this->well,
            'vessel' => $this->vessel,
        ];
    }
}
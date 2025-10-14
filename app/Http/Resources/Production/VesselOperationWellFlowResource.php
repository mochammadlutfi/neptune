<?php
namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VesselOperationWellFlowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vessel_operation_id' => $this->vessel_operation_id,
            'well_id' => $this->well_id,
            'gas_flow_rate_mmscfd' => $this->gas_flow_rate_mmscfd,
            
            // Well info
            'well' => $this->when($this->relationLoaded('well'), [
                'id' => $this->well?->id,
                'code' => $this->well?->code,
                'name' => $this->well?->name,
                'type' => $this->well?->type,
            ]),
        ];
    }
}
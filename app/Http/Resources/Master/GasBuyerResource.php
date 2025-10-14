<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GasBuyerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
                    'code' => $this->vessel->code,
                ];
            }),
            'code' => $this->code,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'status_label' => $this->is_active ? 'Active' : 'Inactive',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
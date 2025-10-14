<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TankResource extends JsonResource
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
                    'type' => $this->vessel->type,
                ];
            }),
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'is_multiphase' => $this->is_multiphase,
            'product_type' => $this->product_type,
            'capacity_bbls' => $this->capacity_bbls,
            'capacity_formatted' => number_format($this->capacity_bbls, 2) . ' bbls',
            'is_active' => $this->is_active,
            'status_label' => $this->is_active ? 'Active' : 'Inactive',
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
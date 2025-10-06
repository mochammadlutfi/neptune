<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VesselResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'oim_id' => $this->oim_id,
            'oim' => $this->whenLoaded('oim', function () {
                return [
                    'id' => $this->oim->id,
                    'name' => $this->oim->name,
                ];
            }),
            'client_name' => $this->client_name,
            'client_oim' => $this->client_oim,
            'is_active' => $this->is_active,
            'status_label' => $this->getStatusLabel(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatProcessingCapacity(): ?string
    {
        $parts = [];
        
        if ($this->oil_processing_capacity_bpd) {
            $parts[] = number_format($this->oil_processing_capacity_bpd) . ' BPD';
        }
        
        if ($this->gas_processing_capacity_mmscfd) {
            $parts[] = number_format($this->gas_processing_capacity_mmscfd, 2) . ' MMSCFD';
        }
        
        return !empty($parts) ? implode(' / ', $parts) : null;
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'Active' => 'Active',
            'Inactive' => 'Inactive',
            'Under Maintenance' => 'Under Maintenance',
            'Decommissioned' => 'Decommissioned',
            default => $this->status ?? 'Unknown'
        };
    }
}
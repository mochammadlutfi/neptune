<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'code' => $this->code,
            'tag' => $this->tag,
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'is_critical' => $this->is_critical,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'installation_date' => $this->installation_date?->format('Y-m-d'),
            'vessel' => $this->whenLoaded('vessel', function () {
                return new VesselResource($this->vessel);
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatCriticalStatus(): string
    {
        $critical = $this->is_critical ? 'Critical' : 'Non-Critical';
        $status = $this->getStatusLabel();
        
        return "{$critical} - {$status}";
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'running' => 'Running',
            'standby' => 'Standby',
            'maintenance' => 'Maintenance',
            'abandoned' => 'Abandoned',
            default => $this->status ?? 'Unknown'
        };
    }

}
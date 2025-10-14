<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellResource extends JsonResource
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
                    'code' => $this->vessel->code,
                ];
            }),
            
            // Basic Information
            'code' => $this->code,
            'name' => $this->name,
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Get status label for display
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'Active' => 'Active',
            'Shut-in' => 'Shut-in',
            'Abandoned' => 'Abandoned',
            'Suspended' => 'Suspended',
            'Testing' => 'Testing',
            'Workover' => 'Workover',
            default => $this->status
        };
    }
}
<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentStatusResource extends JsonResource
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
            'equipment_id' => $this->equipment_id,
            'equipment_name' => $this->equipment?->name,
            'equipment_code' => $this->equipment?->code,
            'equipment_type' => $this->equipment?->type,
            'reading_time' => $this->reading_time?->format('Y-m-d H:i:s'),
            'reading_date' => $this->reading_time?->format('Y-m-d'),
            'reading_time_only' => $this->reading_time?->format('H:i:s'),
            'operational_status' => $this->operational_status,
            'shift' => $this->shift,
            'parameters' => $this->parameters ?? [],
            'parameters_formatted' => $this->formatParameters(),
            'remarks' => $this->remarks,
            'recorded_by' => $this->recorded_by,
            'recorded_by_name' => $this->recordedBy?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relasi lengkap
            'vessel' => $this->whenLoaded('vessel'),
            'equipment' => $this->whenLoaded('equipment'),
            'recorded_by_user' => $this->whenLoaded('recordedBy'),
        ];
    }

    /**
     * Format parameters untuk display yang lebih mudah dibaca.
     *
     * @return array
     */
    private function formatParameters(): array
    {
        if (!is_array($this->parameters) || empty($this->parameters)) {
            return [];
        }

        $formatted = [];
        foreach ($this->parameters as $param) {
            if (isset($param['name']) && isset($param['value'])) {
                $unit = isset($param['unit']) && !empty($param['unit']) ? " {$param['unit']}" : '';
                $formatted[] = [
                    'name' => $param['name'],
                    'value' => $param['value'],
                    'unit' => $param['unit'] ?? null,
                    'display' => "{$param['name']}: {$param['value']}{$unit}"
                ];
            }
        }

        return $formatted;
    }
}
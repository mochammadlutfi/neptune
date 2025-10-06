<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonnelPositionResource extends JsonResource
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
            'department' => $this->department,
            'level' => $this->level,
            'department_level' => $this->department && $this->level ? "{$this->department} - {$this->level}" : ($this->department ?: $this->level),
            'is_essential' => $this->is_essential,
            'is_minimum_manning' => $this->is_minimum_manning,
            'flags' => $this->formatFlags(),
            'min_experience_years' => $this->min_experience_years,
            'required_certificates' => $this->required_certificates,
            'certificates_count' => is_array($this->required_certificates) ? count($this->required_certificates) : 0,
            'is_active' => $this->is_active,
            'status_label' => $this->is_active ? 'Active' : 'Inactive',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatFlags(): string
    {
        $flags = [];
        
        if ($this->is_essential) {
            $flags[] = 'Essential';
        } else {
            $flags[] = 'Non-Essential';
        }
        
        if ($this->is_minimum_manning) {
            $flags[] = 'Min Manning';
        } else {
            $flags[] = 'Optional';
        }
        
        return implode(' + ', $flags);
    }
}
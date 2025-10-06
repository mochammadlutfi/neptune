<?php

namespace App\Http\Resources\Equipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceActivitiesResource extends JsonResource
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
            'equipment_tag' => $this->equipment?->tag,
            'equipment_code' => $this->equipment?->code,
            'activity_date' => $this->activity_date?->format('Y-m-d'),
            'work_order_no' => $this->work_order_no,
            'work_type' => $this->work_type,
            'description' => $this->description,
            'work_hours' => $this->work_hours ? (float) $this->work_hours : null,
            'manpower_count' => $this->manpower_count ? (int) $this->manpower_count : null,
            'status' => $this->status,
            'status_badge_class' => $this->getStatusBadgeClass(),
            'completed_by' => $this->completed_by,
            'completion_date' => $this->completion_date?->format('Y-m-d'),
            'recorded_by' => $this->recorded_by,
            'recorded_by_name' => $this->recordedBy?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Calculated fields
            'is_overdue' => $this->isOverdue(),
            'days_since_due' => $this->daysSinceDue(),
            'duration_display' => $this->getDurationDisplay(),

            // Related models
            'vessel' => $this->whenLoaded('vessel'),
            'equipment' => $this->whenLoaded('equipment'),
            'recordedBy' => $this->whenLoaded('recordedBy'),
        ];
    }

    /**
     * Get CSS class for status badge
     */
    private function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'Planned' => 'badge-info',
            'In Progress' => 'badge-warning',
            'Completed' => 'badge-success',
            'Deferred' => 'badge-secondary',
            'Cancelled' => 'badge-danger',
            default => 'badge-light'
        };
    }

    /**
     * Check if maintenance is overdue
     */
    private function isOverdue(): bool
    {
        if ($this->status === 'Completed' || $this->status === 'Cancelled') {
            return false;
        }

        return $this->activity_date && $this->activity_date < now()->startOfDay();
    }

    /**
     * Get days since due date
     */
    private function daysSinceDue(): ?int
    {
        if (!$this->activity_date || $this->status === 'Completed' || $this->status === 'Cancelled') {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->activity_date, false);
    }

    /**
     * Get duration display text
     */
    private function getDurationDisplay(): ?string
    {
        if (!$this->work_hours) {
            return null;
        }

        $hours = floor($this->work_hours);
        $minutes = ($this->work_hours - $hours) * 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }
}
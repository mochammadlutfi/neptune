<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GasSalesResource extends JsonResource
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
                    'code' => $this->vessel->code ?? null,
                ];
            }),
            'gas_buyer_id' => $this->gas_buyer_id,
            'gas_buyer' => $this->whenLoaded('gasBuyer', function () {
                return [
                    'id' => $this->gasBuyer->id,
                    'name' => $this->gasBuyer->name,
                    'code' => $this->gasBuyer->code ?? null,
                    'company' => $this->gasBuyer->company ?? null,
                ];
            }),
            'sales_date' => $this->sales_date?->format('Y-m-d'),
            'export_pressure_psi' => $this->export_pressure_psi ? (float) $this->export_pressure_psi : null,
            'export_temp_f' => $this->export_temp_f ? (float) $this->export_temp_f : null,
            'export_temp_c' => $this->export_temp_f ? round(($this->export_temp_f - 32) * 5/9, 2) : null,
            'actual_delivery_mmscf' => $this->actual_delivery_mmscf ? (float) $this->actual_delivery_mmscf : null,
            'nomination_mmscf' => $this->nomination_mmscf ? (float) $this->nomination_mmscf : null,
            'variance_mmscf' => $this->actual_delivery_mmscf && $this->nomination_mmscf 
                ? (float) ($this->actual_delivery_mmscf - $this->nomination_mmscf) 
                : null,
            'variance_percent' => $this->actual_delivery_mmscf && $this->nomination_mmscf && $this->nomination_mmscf > 0
                ? round((($this->actual_delivery_mmscf - $this->nomination_mmscf) / $this->nomination_mmscf) * 100, 2)
                : null,
            'delivery_performance' => $this->actual_delivery_mmscf && $this->nomination_mmscf && $this->nomination_mmscf > 0
                ? ($this->actual_delivery_mmscf / $this->nomination_mmscf >= 0.95 ? 'Good' : 'Below Target')
                : null,
            'heating_value_btu' => $this->heating_value_btu ? (float) $this->heating_value_btu : null,
            'heating_value_mj' => $this->heating_value_btu ? round($this->heating_value_btu * 0.001055, 2) : null,
            'specific_gravity' => $this->specific_gravity ? (float) $this->specific_gravity : null,
            'gas_quality' => [
                'h2s_content_ppm' => $this->h2s_content_ppm ? (float) $this->h2s_content_ppm : null,
                'co2_content_pct' => $this->co2_content_pct ? (float) $this->co2_content_pct : null,
                'moisture_content_ppm' => $this->moisture_content_ppm ? (float) $this->moisture_content_ppm : null,
                'quality_grade' => $this->getQualityGrade(),
            ],
            'energy_content_mmbtu' => $this->actual_delivery_mmscf && $this->heating_value_btu
                ? round($this->actual_delivery_mmscf * $this->heating_value_btu / 1000, 2)
                : null,
            'recorded_by' => $this->recorded_by,
            'recorded_by_user' => $this->whenLoaded('recordedBy', function () {
                return [
                    'id' => $this->recordedBy->id,
                    'name' => $this->recordedBy->name,
                    'email' => $this->recordedBy->email,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Determine gas quality grade based on composition
     */
    private function getQualityGrade(): ?string
    {
        $h2s = $this->h2s_content_ppm ?? 0;
        $co2 = $this->co2_content_pct ?? 0;
        $moisture = $this->moisture_content_ppm ?? 0;

        // Pipeline quality standards
        if ($h2s <= 4 && $co2 <= 2 && $moisture <= 112) {
            return 'Pipeline Quality';
        } elseif ($h2s <= 10 && $co2 <= 5 && $moisture <= 200) {
            return 'Commercial Grade';
        } elseif ($h2s <= 20 && $co2 <= 10 && $moisture <= 400) {
            return 'Industrial Grade';
        } else {
            return 'Below Standard';
        }
    }
}
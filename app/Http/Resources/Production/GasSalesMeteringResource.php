<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GasSalesMeteringResource extends JsonResource
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
            'vessel_name' => $this->vessel->name ?? null,
            'reading_time' => $this->reading_time?->format('Y-m-d H:i:s'),
            'export_pressure_psi' => $this->export_pressure_psi,
            'export_temp_f' => $this->export_temp_f,
            'flowrate_mmscfd' => $this->flowrate_mmscfd,
            'total_volume_mmscf' => $this->total_volume_mmscf,
            'heating_value_btu_scf' => $this->heating_value_btu_scf,
            'specific_gravity' => $this->specific_gravity,
            'h2s_content_ppm' => $this->h2s_content_ppm,
            'co2_content_percent' => $this->co2_content_percent,
            'buyer_name' => $this->buyer_name,
            'nomination_mmscf' => $this->nomination_mmscf,
            'actual_delivery_mmscf' => $this->actual_delivery_mmscf,
            'variance_percent' => $this->variance_percent,
            'recorded_by' => $this->recorded_by,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function getVarianceStatus(): string
    {
        if (!$this->variance_percent) {
            return 'No Variance Data';
        }
        
        $variance = abs($this->variance_percent);
        
        if ($variance <= 2) {
            return 'Within Tolerance';
        } elseif ($variance <= 5) {
            return 'Minor Variance';
        } elseif ($variance <= 10) {
            return 'Significant Variance';
        } else {
            return 'Major Variance';
        }
    }
}
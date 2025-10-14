<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SalesGasMeteringDailyResource extends JsonResource
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
            'name' => $this->name,
            'vessel_id' => $this->vessel_id,
            'vessel' => $this->whenLoaded('vessel', function () {
                return [
                    'id' => $this->vessel->id,
                    'name' => $this->vessel->name,
                    'code' => $this->vessel->code ?? null,
                ];
            }),
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'pressure_psig' => $this->pressure_psig ? (float) $this->pressure_psig : null,
            'temperature_f' => $this->temperature_f ? (float) $this->temperature_f : null,
            'h2o_lb_mmscf' => $this->h2o_lb_mmscf ? (float) $this->h2o_lb_mmscf : null,
            'co2_mol_pct' => $this->co2_mol_pct ? (float) $this->co2_mol_pct : null,
            'ghv' => $this->ghv ? (float) $this->ghv : null,
            'specific_gravity' => $this->specific_gravity ? (float) $this->specific_gravity : null,
            'ejgp_pressure_psig' => $this->ejgp_pressure_psig ? (float) $this->ejgp_pressure_psig : null,
            'hdcp' => $this->hdcp ? json_decode($this->hdcp, true) : [],
            'flowrates' => $this->whenLoaded('flowrates', function () {
                return $this->flowrates->map(function ($line) {
                    return [
                        'id' => $line->id,
                        'buyer_id' => $line->buyer_id,
                        'buyer' => $line->relationLoaded('buyer') ? [
                            'id' => $line->buyer->id,
                            'name' => $line->buyer->name,
                            'code' => $line->buyer->code ?? null,
                        ] : null,
                        'primary_stream' => $line->primary_stream ? (float) $line->primary_stream : null,
                        'backup_stream' => $line->backup_stream ? (float) $line->backup_stream : null,
                    ];
                });
            }),
            'total_flowrates' => $this->total_flowrates ? (float) $this->total_flowrates : null,
            'total_readings' => $this->total_readings ? (int) $this->total_readings : null,
            'data_completeness_pct' => $this->data_completeness_pct ? (float) $this->data_completeness_pct : null,
            'last_calculated_at' => $this->last_calculated_at?->format('Y-m-d H:i:s'),
            'created_uid' => $this->created_uid,
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ];
            }),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
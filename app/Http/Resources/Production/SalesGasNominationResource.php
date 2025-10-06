<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesGasNominationResource extends JsonResource
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
            'date' => $this->date,
            'total_nomination' => $this->total_nomination ? (float) $this->total_nomination : null,
            'total_confirmed' => $this->total_confirmed ? (float) $this->total_confirmed : null,
            'status' => $this->status,
            'recorded_by' => $this->recorded_by,
            'recorded_by_user' => $this->whenLoaded('recordedBy', function () {
                return [
                    'id' => $this->recordedBy->id,
                    'name' => $this->recordedBy->name,
                    'email' => $this->recordedBy->email,
                ];
            }),
            'lines' => $this->whenLoaded('lines', function () {
                return $this->lines->map(function ($line) {
                    return [
                        'id' => $line->id,
                        'gas_buyer_id' => $line->gas_buyer_id,
                        'gas_buyer' => $line->whenLoaded('gasBuyer', function () {
                            return [
                                'id' => $line->gasBuyer->id,
                                'name' => $line->gasBuyer->name,
                                'code' => $line->gasBuyer->code ?? null,
                                'company' => $line->gasBuyer->company ?? null,
                            ];
                        }),
                        'nomination' => $line->nomination ? (float) $line->nomination : null,
                        'confirmed' => $line->confirmed ? (float) $line->confirmed : null,
                    ];
                });
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
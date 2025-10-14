<?php

namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
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
                        'buyer_id' => $line->buyer_id,
                        'buyer' => $line->relationLoaded('buyer') ? [
                            'id' => $line->buyer->id,
                            'name' => $line->buyer->name,
                            'code' => $line->buyer->code ?? null,
                        ] : null,
                        'nomination' => $line->nomination ? (float) $line->nomination : null,
                        'confirmed' => $line->confirmed ? (float) $line->confirmed : null,
                    ];
                });
            }),
            'created_uid' => $this->created_uid,
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ];
            }),
            'remarks' => $this->remarks,
            'confirmed_at' => $this->confirmed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
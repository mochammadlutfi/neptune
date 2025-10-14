<?php
namespace App\Http\Resources\Production;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VesselOperationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vessel_id' => $this->vessel_id,
            'date' => $this->date->format('Y-m-d'),
            
            // Gas Operations
            'inlet_gas_mmscfd' => $this->inlet_gas_mmscfd,
            'total_sales_gas_mmscfd' => $this->total_sales_gas_mmscfd,
            'fuel_gas_mmscfd' => $this->fuel_gas_mmscfd,
            'flare_hp_mmscfd' => $this->flare_hp_mmscfd,
            'flare_lp_mmscfd' => $this->flare_lp_mmscfd,
            'total_flare_gas_mmscfd' => $this->total_flare_gas_mmscfd,
            'gas_export_uptime' => $this->gas_export_uptime ? $this->gas_export_uptime->format('H:i:s') : null,
            'gas_export_uptime_percent' => $this->gas_export_uptime_percent,
            'inlet_pressure_psi' => $this->inlet_pressure_psi,
            'inlet_temp_f' => $this->inlet_temp_f,
            'outlet_pressure_psi' => $this->outlet_pressure_psi,
            'outlet_temp_f' => $this->outlet_temp_f,
            
            // Condensate Operations
            'condensate_produced_lts' => $this->condensate_produced_lts,
            'condensate_produced_bbls' => $this->condensate_produced_bbls,
            'condensate_skimmed_bbls' => $this->condensate_skimmed_bbls,
            'condensate_production_total_bbls' => $this->condensate_production_total_bbls,
            'condensate_stock_bbls' => $this->condensate_stock_bbls,
            'condensate_consumed_gtg_bbls' => $this->condensate_consumed_gtg_bbls,
            'condensate_temp_f' => $this->condensate_temp_f,
            'condensate_uptime' => $this->condensate_uptime ? $this->condensate_uptime->format('H:i:s') : null,
            'condensate_uptime_percent' => $this->condensate_uptime_percent,
            
            // Diesel Fuel
            'diesel_fuel_mopu_ltr' => $this->diesel_fuel_mopu_ltr,
            'diesel_fuel_hcml_ltr' => $this->diesel_fuel_hcml_ltr,
            
            // Water Operations
            'produced_water_total_bbls' => $this->produced_water_total_bbls,
            'produced_water_offspec_bbls' => $this->produced_water_offspec_bbls,
            'produced_water_overboard_bbls' => $this->produced_water_overboard_bbls,
            'water_oiw_content_ppm' => $this->water_oiw_content_ppm,
            
            // Well Flows
            'well_flows' => VesselOperationWellFlowResource::collection($this->whenLoaded('wellFlows')),
            'total_well_flow' => $this->when($this->relationLoaded('wellFlows'), $this->total_well_flow),
            'is_balanced' => $this->when($this->relationLoaded('wellFlows'), $this->is_balanced),
            
            // Relationships
            'vessel' => [
                'id' => $this->vessel->id,
                'code' => $this->vessel->code,
                'name' => $this->vessel->name,
                'type' => $this->vessel->type,
            ],
            
            'recorded_by' => $this->when($this->recordedBy, [
                'id' => $this->recordedBy?->id,
                'name' => $this->recordedBy?->name,
                'email' => $this->recordedBy?->email,
            ]),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
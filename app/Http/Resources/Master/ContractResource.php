<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'contract_number' => $this->contract_number,
            'contract_name' => $this->contract_name,
            'contract_type' => $this->contract_type,
            'operator_name' => $this->operator_name,
            'kkks_representative' => $this->kkks_representative,
            'partner_companies' => $this->partner_companies,
            'effective_date' => $this->effective_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'extension_options' => $this->extension_options,
            'field_name' => $this->field_name,
            'block_name' => $this->block_name,
            'working_area_km2' => $this->working_area_km2,
            'cost_recovery_limit_pct' => $this->cost_recovery_limit_pct,
            'ftp_share_pct' => $this->ftp_share_pct,
            'contractor_share_oil_pct' => $this->contractor_share_oil_pct,
            'contractor_share_gas_pct' => $this->contractor_share_gas_pct,
            'government_share_oil_pct' => $this->government_share_oil_pct,
            'government_share_gas_pct' => $this->government_share_gas_pct,
            'minimum_work_commitment' => $this->minimum_work_commitment,
            'minimum_expenditure_usd' => $this->minimum_expenditure_usd,
            'local_content_requirement_pct' => $this->local_content_requirement_pct,
            'performance_bond_amount_usd' => $this->performance_bond_amount_usd,
            'bond_expiry_date' => $this->bond_expiry_date?->format('Y-m-d'),
            'contract_status' => $this->contract_status,
            
            // Computed attributes dari model
            'days_to_expiry' => $this->days_to_expiry,
            'is_expiring_soon' => $this->is_expiring_soon,
            'oil_shares_total' => $this->oil_shares_total,
            'gas_shares_total' => $this->gas_shares_total,
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships (hanya dimuat jika ada)
            'contract_targets_count' => $this->whenCounted('contractTargets'),
            'daily_performances_count' => $this->whenCounted('dailyPerformances'),
            
            // Include relationships jika dimuat
            'contract_targets' => $this->whenLoaded('contractTargets'),
            'daily_performances' => $this->whenLoaded('dailyPerformances'),
        ];
    }
}
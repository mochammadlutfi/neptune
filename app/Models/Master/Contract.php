<?php

namespace App\Models\Master;

use App\Models\Contract\ContractTarget;
use App\Models\Contract\DailyContractPerformance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $fillable = [
        'contract_number',
        'contract_name',
        'contract_type',
        'operator_name',
        'kkks_representative',
        'partner_companies',
        'effective_date',
        'expiry_date',
        'extension_options',
        'field_name',
        'block_name',
        'working_area_km2',
        'cost_recovery_limit_pct',
        'ftp_share_pct',
        'contractor_share_oil_pct',
        'contractor_share_gas_pct',
        'government_share_oil_pct',
        'government_share_gas_pct',
        'minimum_work_commitment',
        'minimum_expenditure_usd',
        'local_content_requirement_pct',
        'performance_bond_amount_usd',
        'bond_expiry_date',
        'contract_status',
    ];

    protected $casts = [
        'partner_companies' => 'json',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'bond_expiry_date' => 'date',
        'cost_recovery_limit_pct' => 'decimal:2',
        'ftp_share_pct' => 'decimal:2',
        'contractor_share_oil_pct' => 'decimal:2',
        'contractor_share_gas_pct' => 'decimal:2',
        'government_share_oil_pct' => 'decimal:2',
        'government_share_gas_pct' => 'decimal:2',
        'working_area_km2' => 'decimal:2',
        'minimum_expenditure_usd' => 'decimal:2',
        'local_content_requirement_pct' => 'decimal:2',
        'performance_bond_amount_usd' => 'decimal:2',
    ];

    protected $appends = [
        'days_to_expiry',
        'is_expiring_soon',
        'oil_shares_total',
        'gas_shares_total',
    ];

    // Relationships
    public function contractTargets()
    {
        return $this->hasMany(ContractTarget::class);
    }

    public function dailyPerformances()
    {
        return $this->hasMany(DailyContractPerformance::class);
    }

    // Accessors
    public function getDaysToExpiryAttribute()
    {
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->days_to_expiry <= 180 && $this->days_to_expiry >= 0;
    }

    public function getOilSharesTotalAttribute()
    {
        return ($this->contractor_share_oil_pct ?? 0) + ($this->government_share_oil_pct ?? 0);
    }

    public function getGasSharesTotalAttribute()
    {
        return ($this->contractor_share_gas_pct ?? 0) + ($this->government_share_gas_pct ?? 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('contract_status', 'Active');
    }

    public function scopeExpiring($query, $months = 6)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addMonths($months));
    }

    public function scopePSC($query)
    {
        return $query->where('contract_type', 'PSC');
    }

    // Methods
    public function isCommercialTermsValid()
    {
        return $this->oil_shares_total == 100 && $this->gas_shares_total == 100;
    }

    public function getCurrentYearPerformance()
    {
        return $this->dailyPerformances()
            ->whereYear('report_date', Carbon::now()->year)
            ->selectRaw('
                SUM(oil_production_bbls) as total_oil_production,
                SUM(gas_production_mscf) as total_gas_production,
                AVG(contractor_oil_share) as avg_contractor_oil_share,
                AVG(contractor_gas_share) as avg_contractor_gas_share
            ')
            ->first();
    }

    public function getCurrentYearTargets()
    {
        return $this->contractTargets()
            ->where('year', Carbon::now()->year)
            ->first();
    }
}

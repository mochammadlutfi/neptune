<?php

namespace App\Services\Settings;

use App\Repositories\Settings\CompanyRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class CompanyService
{
    protected $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getCompanies(array $filters)
    {
        return $this->companyRepository->all($filters);
    }

    public function createCompany(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->companyRepository->create($data);
        });
    }

    public function findCompany(int $id)
    {
        return $this->companyRepository->findById($id);
    }

    public function updateCompany(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->companyRepository->update($id, $data);
        });
    }

    public function deleteCompany(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->companyRepository->delete($id);
        });
    }

    public function bulkDeleteCompanies(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            return $this->companyRepository->bulkDelete($ids);
        });
    }

    public function getCompanyStats()
    {
        return $this->companyRepository->getStats();
    }

    /**
     * Get company settings
     */
    public function getCompanySettings(int $companyId)
    {
        $company = Company::findOrFail($companyId);
        
        // Get all company-specific settings
        $settings = [
            // Accounting Settings
            'account_revenue' => $company->setting('account_revenue'),
            'account_receivable' => $company->setting('account_receivable'),
            'account_tax_collected' => $company->setting('account_tax_collected'),
            'account_expense' => $company->setting('account_expense'),
            'account_payable' => $company->setting('account_payable'),
            'account_tax_paid' => $company->setting('account_tax_paid'),
            'account_inventory' => $company->setting('account_inventory'),
            'account_cogs' => $company->setting('account_cogs'),
            
            // Sales Settings
            'sales_invoice_policy' => $company->setting('sales_invoice_policy', 'ordered_qty'),
            'sales_location_id' => $company->setting('sales_location_id'),
            'sales_payment_term_id' => $company->setting('sales_payment_term_id'),
            
            // Purchase Settings
            'purchase_bill_policy' => $company->setting('purchase_bill_policy', 'ordered_qty'),
            'purchase_location_id' => $company->setting('purchase_location_id'),
            'purchase_payment_term_id' => $company->setting('purchase_payment_term_id'),
            
            // Inventory Settings
            'inventory_valuation' => $company->setting('inventory_valuation', 'fifo'),
            'default_warehouse_id' => $company->setting('default_warehouse_id'),
            'allow_negative_stock' => $company->setting('allow_negative_stock', false),
        ];
        
        return $settings;
    }

    /**
     * Update company settings
     */
    public function updateCompanySettings(int $companyId, array $settings)
    {
        return DB::transaction(function () use ($companyId, $settings) {
            $company = Company::findOrFail($companyId);
            
            // Update each setting
            foreach ($settings as $key => $value) {
                $company->setSetting($key, $value);
            }
            
            return $settings;
        });
    }
}

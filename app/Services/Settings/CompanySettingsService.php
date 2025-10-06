<?php

namespace App\Services\Settings;

use App\Repositories\Settings\CompanySettingsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Helpers\CommonHelper;

class CompanySettingsService
{
    protected $repository;

    public function __construct(CompanySettingsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get companies list with pagination
     */
    public function getCompanies(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Find company by ID
     */
    public function findCompany(int $id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Create new company
     */
    public function createCompany(array $data)
    {
        DB::beginTransaction();
        try {
            $company = $this->repository->create($data);
            
            // Set default settings for new company
            $this->setDefaultSettings($company->id);
            
            DB::commit();
            return $company;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update company
     */
    public function updateCompany(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete company
     */
    public function deleteCompany(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Get company statistics
     */
    public function getCompanyStats()
    {
        return $this->repository->getStats();
    }

    /**
     * Bulk delete companies
     */
    public function bulkDeleteCompanies(array $ids)
    {
        return $this->repository->bulkDelete($ids);
    }

    /**
     * Get all company settings
     */
    public function getSettings(int $companyId, string $category = null)
    {
        return $this->repository->getSettings($companyId, $category);
    }

    /**
     * Get general settings for company
     */
    public function getGeneralSettings(int $companyId)
    {
        return $this->repository->getGeneralSettings($companyId);
    }

    /**
     * Update general settings for company
     */
    public function updateGeneralSettings(int $companyId, array $data)
    {
        DB::beginTransaction();
        try {
            // Handle file uploads
            $settingsData = [];
            
            // Basic company info
            if (isset($data['company_name'])) {
                $settingsData['company_name'] = $data['company_name'];
            }
            if (isset($data['company_email'])) {
                $settingsData['company_email'] = $data['company_email'];
            }
            if (isset($data['company_phone'])) {
                $settingsData['company_phone'] = $data['company_phone'];
            }
            if (isset($data['company_address'])) {
                $settingsData['company_address'] = $data['company_address'];
            }
            
            // Handle logo uploads
            if (isset($data['logo_light']) && $data['logo_light'] instanceof UploadedFile) {
                // Delete old logo if exists
                $company = $this->findCompany($companyId);
                $oldLogo = $company->settings()->get('logo_light');
                if ($oldLogo) {
                    deleteFile($oldLogo);
                }
                
                $settingsData['logo_light'] = uploadFile($data['logo_light'], 'company/logos', true);
            }
            
            if (isset($data['logo_dark']) && $data['logo_dark'] instanceof UploadedFile) {
                $company = $this->findCompany($companyId);
                $oldLogo = $company->settings()->get('logo_dark');
                if ($oldLogo) {
                    deleteFile($oldLogo);
                }
                
                $settingsData['logo_dark'] = uploadFile($data['logo_dark'], 'company/logos', true);
            }
            
            if (isset($data['logo_light_sm']) && $data['logo_light_sm'] instanceof UploadedFile) {
                $company = $this->findCompany($companyId);
                $oldLogo = $company->settings()->get('logo_light_sm');
                if ($oldLogo) {
                    deleteFile($oldLogo);
                }
                
                $settingsData['logo_light_sm'] = uploadFile($data['logo_light_sm'], 'company/logos', true);
            }
            
            if (isset($data['logo_dark_sm']) && $data['logo_dark_sm'] instanceof UploadedFile) {
                $company = $this->findCompany($companyId);
                $oldLogo = $company->settings()->get('logo_dark_sm');
                if ($oldLogo) {
                    deleteFile($oldLogo);
                }
                
                $settingsData['logo_dark_sm'] = uploadFile($data['logo_dark_sm'], 'company/logos', true);
            }

            // Other settings
            if (isset($data['currency_id'])) {
                $settingsData['currency_id'] = $data['currency_id'];
            }
            if (isset($data['date_format'])) {
                $settingsData['date_format'] = $data['date_format'];
            }
            if (isset($data['time_format'])) {
                $settingsData['time_format'] = $data['time_format'];
            }
            if (isset($data['timezone'])) {
                $settingsData['timezone'] = $data['timezone'];
            }
            if (isset($data['locale'])) {
                $settingsData['locale'] = $data['locale'];
            }

            $this->repository->updateSettings($companyId, 'general', $settingsData);
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get accounting settings
     */
    public function getAccountingSettings(int $companyId)
    {
        return $this->repository->getAccountingSettings($companyId);
    }

    /**
     * Update accounting settings
     */
    public function updateAccountingSettings(int $companyId, array $data)
    {
        return $this->repository->updateSettings($companyId, 'accounting', $data);
    }

    /**
     * Get sales settings
     */
    public function getSalesSettings(int $companyId)
    {
        return $this->repository->getSalesSettings($companyId);
    }

    /**
     * Update sales settings
     */
    public function updateSalesSettings(int $companyId, array $data)
    {
        return $this->repository->updateSettings($companyId, 'sales', $data);
    }

    /**
     * Get purchase settings
     */
    public function getPurchaseSettings(int $companyId)
    {
        return $this->repository->getPurchaseSettings($companyId);
    }

    /**
     * Update purchase settings
     */
    public function updatePurchaseSettings(int $companyId, array $data)
    {
        return $this->repository->updateSettings($companyId, 'purchase', $data);
    }

    /**
     * Get inventory settings
     */
    public function getInventorySettings(int $companyId)
    {
        return $this->repository->getInventorySettings($companyId);
    }

    /**
     * Update inventory settings
     */
    public function updateInventorySettings(int $companyId, array $data)
    {
        return $this->repository->updateSettings($companyId, 'inventory', $data);
    }

    /**
     * Set default settings for new company
     */
    private function setDefaultSettings(int $companyId)
    {
        $company = $this->repository->findById($companyId);
        
        // Set default general settings
        $company->settings()->set('currency_id', settings()->get('currency_id', 1));
        $company->settings()->set('date_format', settings()->get('date_format', 'Y-m-d'));
        $company->settings()->set('time_format', settings()->get('time_format', 'H:i'));
        $company->settings()->set('timezone', settings()->get('timezone', 'UTC'));
        $company->settings()->set('locale', settings()->get('locale', 'en'));
        
        // Set default sales settings
        $company->settings()->set('invoicing_policy', 'ordered_qty');
        $company->settings()->set('sale_payment_term', 30);
        
        // Set default purchase settings
        $company->settings()->set('bill_controll', 'received_qty');
        $company->settings()->set('purchase_payment_term', 20);
        
        // Set default inventory settings
        $company->settings()->set('inventory_method', 'FIFO');
        $company->settings()->set('auto_reorder', false);
        $company->settings()->set('reorder_level', 10);
    }
}

<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait ActivityLoggerHelper
{
    use ActivityLogger;

    /**
     * Log well production activities
     * 
     * @param string $action Action type (created, updated, deleted)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logWellProductionActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.well_production.' . $action, $params);
        
        switch ($action) {
            case 'created':
                $this->logCreate($model, $request, $message);
                break;
            case 'updated':
                $originalAttributes = $model->getOriginal();
                $this->logUpdate($model, $request, $originalAttributes, $message);
                break;
            case 'deleted':
                $this->logDelete($model, $request, $message);
                break;
        }
    }

    /**
     * Log equipment activities
     * 
     * @param string $action Action type (created, updated, deleted)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logEquipmentActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.equipment.' . $action, $params);
        
        switch ($action) {
            case 'created':
                $this->logCreate($model, $request, $message);
                break;
            case 'updated':
                $originalAttributes = $model->getOriginal();
                $this->logUpdate($model, $request, $originalAttributes, $message);
                break;
            case 'deleted':
                $this->logDelete($model, $request, $message);
                break;
        }
    }

    /**
     * Log HSE activities
     * 
     * @param string $action Action type (incident_created, incident_updated, etc.)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logHSEActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.hse.' . $action, $params);
        
        if (str_contains($action, 'created') || str_contains($action, 'pob_created')) {
            $this->logCreate($model, $request, $message);
        } elseif (str_contains($action, 'updated') || str_contains($action, 'pob_updated')) {
            $originalAttributes = $model->getOriginal();
            $this->logUpdate($model, $request, $originalAttributes, $message);
        } elseif (str_contains($action, 'deleted') || str_contains($action, 'pob_deleted')) {
            $this->logDelete($model, $request, $message);
        }
    }

    /**
     * Log marine activities
     * 
     * @param string $action Action type (ballast_created, weather_created, etc.)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logMarineActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.marine.' . $action, $params);
        
        if (str_contains($action, 'created')) {
            $this->logCreate($model, $request, $message);
        } elseif (str_contains($action, 'updated')) {
            $originalAttributes = $model->getOriginal();
            $this->logUpdate($model, $request, $originalAttributes, $message);
        } elseif (str_contains($action, 'deleted')) {
            $this->logDelete($model, $request, $message);
        }
    }

    /**
     * Log fuel activities
     * 
     * @param string $action Action type (consumption_created, transfer_created, etc.)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logFuelActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.fuel.' . $action, $params);
        
        if (str_contains($action, 'created')) {
            $this->logCreate($model, $request, $message);
        } elseif (str_contains($action, 'updated')) {
            $originalAttributes = $model->getOriginal();
            $this->logUpdate($model, $request, $originalAttributes, $message);
        } elseif (str_contains($action, 'deleted')) {
            $this->logDelete($model, $request, $message);
        }
    }

    /**
     * Log flare activities
     * 
     * @param string $action Action type (created, updated, deleted)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logFlareActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.flare.' . $action, $params);
        
        switch ($action) {
            case 'created':
                $this->logCreate($model, $request, $message);
                break;
            case 'updated':
                $originalAttributes = $model->getOriginal();
                $this->logUpdate($model, $request, $originalAttributes, $message);
                break;
            case 'deleted':
                $this->logDelete($model, $request, $message);
                break;
        }
    }

    /**
     * Log master data activities
     * 
     * @param string $action Action type (vessel_created, well_created, etc.)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logMasterDataActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.master_data.' . $action, $params);
        
        if (str_contains($action, 'created')) {
            $this->logCreate($model, $request, $message);
        } elseif (str_contains($action, 'updated')) {
            $originalAttributes = $model->getOriginal();
            $this->logUpdate($model, $request, $originalAttributes, $message);
        } elseif (str_contains($action, 'deleted')) {
            $this->logDelete($model, $request, $message);
        }
    }

    /**
     * Log user management activities
     * 
     * @param string $action Action type (user_created, role_assigned, etc.)
     * @param Model $model Model instance
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logUserManagementActivity(string $action, Model $model, Request $request, array $params = [])
    {
        $message = __('activity.user_management.' . $action, $params);
        
        if (str_contains($action, 'created') || str_contains($action, 'assigned')) {
            $this->logCreate($model, $request, $message);
        } elseif (str_contains($action, 'updated')) {
            $originalAttributes = $model->getOriginal();
            $this->logUpdate($model, $request, $originalAttributes, $message);
        } elseif (str_contains($action, 'deleted') || str_contains($action, 'removed')) {
            $this->logDelete($model, $request, $message);
        }
    }

    /**
     * Log authentication activities
     * 
     * @param string $action Action type (login, logout, password_changed, etc.)
     * @param Request $request Request instance
     * @param Model|null $model Model instance (optional)
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logAuthActivity(string $action, Request $request, Model $model = null, array $params = [])
    {
        $message = __('activity.authentication.' . $action, $params);
        $this->logActivity($message, $model, $request);
    }

    /**
     * Log report activities
     * 
     * @param string $action Action type (dvr_generated, dcr_generated, etc.)
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logReportActivity(string $action, Request $request, array $params = [])
    {
        $message = __('activity.reports.' . $action, $params);
        $this->logActivity($message, null, $request);
    }

    /**
     * Log system activities
     * 
     * @param string $action Action type (backup_created, maintenance_started, etc.)
     * @param Request $request Request instance
     * @param array $params Additional parameters for localization
     * @return void
     */
    protected function logSystemActivity(string $action, Request $request, array $params = [])
    {
        $message = __('activity.system.' . $action, $params);
        $this->logActivity($message, null, $request);
    }
}
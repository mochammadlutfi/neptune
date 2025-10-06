<?php

namespace App\Traits;

use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Services\LocalizationService;

trait ActivityLogger
{
    /**
     * Log aktivitas create untuk model
     * 
     * @param Model $model Model yang dibuat
     * @param Request $request Request data
     * @param string $description Deskripsi aktivitas (opsional)
     * @return void
     */
    protected function logCreate(Model $model, Request $request, string $description = null)
    {
        $modelName = class_basename($model);
        $locale = LocalizationService::getActivityLogLocale();
        
        // Use localized description if not provided
        if (!$description) {
            $description = LocalizationService::getActivityMessage(
                'activity.general.created',
                ['model' => $modelName],
                $locale
            );
        }
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'attributes' => $model->getAttributes(),
                'request_data' => $this->filterSensitiveData($request->all()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'locale' => $locale,
                'timestamp' => now()->toISOString()
            ])
            ->log($description);
    }

    /**
     * Log aktivitas update untuk model
     * 
     * @param Model $model Model yang diupdate
     * @param Request $request Request data
     * @param array $originalAttributes Atribut asli sebelum update
     * @param string $description Deskripsi aktivitas (opsional)
     * @return void
     */
    protected function logUpdate(Model $model, Request $request, array $originalAttributes, string $description = null)
    {
        $modelName = class_basename($model);
        $locale = LocalizationService::getActivityLogLocale();
        
        // Use localized description if not provided
        if (!$description) {
            $description = LocalizationService::getActivityMessage(
                'activity.general.updated',
                ['model' => $modelName],
                $locale
            );
        }
        
        $changes = [];
        foreach ($model->getAttributes() as $key => $value) {
            if (isset($originalAttributes[$key]) && $originalAttributes[$key] != $value) {
                $changes[$key] = [
                    'old' => $originalAttributes[$key],
                    'new' => $value
                ];
            }
        }
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'attributes' => $model->getAttributes(),
                'old' => $originalAttributes,
                'changes' => $changes,
                'request_data' => $this->filterSensitiveData($request->all()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'locale' => $locale,
                'timestamp' => now()->toISOString(),
                'changes_count' => count($changes)
            ])
            ->log($description);
    }

    /**
     * Log aktivitas delete untuk model
     * 
     * @param Model $model Model yang dihapus
     * @param Request $request Request data
     * @param string $description Deskripsi aktivitas (opsional)
     * @return void
     */
    protected function logDelete(Model $model, Request $request, string $description = null)
    {
        $modelName = class_basename($model);
        $locale = LocalizationService::getActivityLogLocale();
        
        // Use localized description if not provided
        if (!$description) {
            $description = LocalizationService::getActivityMessage(
                'activity.general.deleted',
                ['model' => $modelName],
                $locale
            );
        }
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'attributes' => $model->getAttributes(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'locale' => $locale,
                'timestamp' => now()->toISOString(),
                'deleted_at' => now()->toISOString()
            ])
            ->log($description);
    }

    /**
     * Log aktivitas custom
     * 
     * @param string $description Deskripsi aktivitas
     * @param Model|null $model Model terkait (opsional)
     * @param Request|null $request Request data (opsional)
     * @param array $properties Properties tambahan (opsional)
     * @return void
     */
    protected function logActivity(string $description, Model $model = null, Request $request = null, array $properties = [])
    {
        $locale = LocalizationService::getActivityLogLocale();
        
        $activity = activity()
            ->causedBy(auth()->user());
            
        if ($model) {
            $activity->performedOn($model);
        }
        
        $defaultProperties = [
            'locale' => $locale,
            'timestamp' => now()->toISOString()
        ];
        
        if ($request) {
            $defaultProperties = array_merge($defaultProperties, [
                'request_data' => $this->filterSensitiveData($request->all()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
        }
        
        $activity->withProperties(array_merge($defaultProperties, $properties))
                 ->log($description);
    }

    /**
     * Filter data sensitif dari request
     * 
     * @param array $data Data request
     * @return array Data yang sudah difilter
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'private_key'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[FILTERED]';
            }
        }
        
        return $data;
    }

    /**
     * Get activity logs untuk model tertentu
     * 
     * @param Model $model Model yang ingin dilihat lognya
     * @param int $limit Limit jumlah log (default: 50)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getActivityLogs(Model $model, int $limit = 50)
    {
        return Activity::forSubject($model)
            ->with('causer')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs untuk user tertentu
     * 
     * @param int|null $userId User ID (default: current user)
     * @param int $limit Limit jumlah log (default: 50)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUserActivityLogs(int $userId = null, int $limit = 50)
    {
        $userId = $userId ?? auth()->id();
        
        return Activity::causedBy($userId)
            ->with(['subject', 'causer'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
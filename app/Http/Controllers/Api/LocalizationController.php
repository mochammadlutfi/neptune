<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class LocalizationController extends Controller
{
    /**
     * Get current locale information
     *
     * @return JsonResponse
     */
    public function getCurrentLocale(): JsonResponse
    {
        try {
            $currentLocale = App::getLocale();
            $localeInfo = LocalizationService::getCurrentLocaleInfo();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'current_locale' => $currentLocale,
                    'locale_info' => $localeInfo,
                    'user_locale' => LocalizationService::getUserLocale(),
                    'activity_log_locale' => LocalizationService::getActivityLogLocale(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current locale information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all supported locales
     *
     * @return JsonResponse
     */
    public function getSupportedLocales(): JsonResponse
    {
        try {
            $supportedLocales = LocalizationService::getSupportedLocales();
            
            return response()->json([
                'success' => true,
                'data' => $supportedLocales
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get supported locales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set user locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setLocale(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'locale' => 'required|string|size:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $locale = $request->input('locale');

            if (!LocalizationService::isValidLocale($locale)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported locale provided',
                    'supported_locales' => array_keys(LocalizationService::getSupportedLocales())
                ], 400);
            }

            $success = LocalizationService::setUserLocale($locale);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => __('Locale updated successfully'),
                    'data' => [
                        'locale' => $locale,
                        'locale_info' => LocalizationService::getCurrentLocaleInfo()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to set locale'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set locale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get localized activity messages
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getActivityMessages(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'keys' => 'required|array',
                'keys.*' => 'required|string',
                'locale' => 'sometimes|string|size:2',
                'params' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $keys = $request->input('keys');
            $locale = $request->input('locale');
            $params = $request->input('params', []);

            $messages = [];
            foreach ($keys as $key) {
                $messages[$key] = LocalizationService::getActivityMessage($key, $params, $locale);
            }

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get activity messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format number according to locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function formatNumber(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|numeric',
                'decimals' => 'sometimes|integer|min:0|max:10',
                'locale' => 'sometimes|string|size:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $number = $request->input('number');
            $decimals = $request->input('decimals', 2);
            $locale = $request->input('locale');

            $formattedNumber = LocalizationService::formatNumber($number, $decimals, $locale);

            return response()->json([
                'success' => true,
                'data' => [
                    'original' => $number,
                    'formatted' => $formattedNumber,
                    'decimals' => $decimals,
                    'locale' => $locale ?? App::getLocale()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to format number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format currency according to locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function formatCurrency(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric',
                'locale' => 'sometimes|string|size:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $amount = $request->input('amount');
            $locale = $request->input('locale');

            $formattedCurrency = LocalizationService::formatCurrency($amount, $locale);

            return response()->json([
                'success' => true,
                'data' => [
                    'original' => $amount,
                    'formatted' => $formattedCurrency,
                    'locale' => $locale ?? App::getLocale()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to format currency',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format date according to locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function formatDate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'include_time' => 'sometimes|boolean',
                'locale' => 'sometimes|string|size:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $date = $request->input('date');
            $includeTime = $request->input('include_time', false);
            $locale = $request->input('locale');

            $formattedDate = LocalizationService::formatDate($date, $locale, $includeTime);

            return response()->json([
                'success' => true,
                'data' => [
                    'original' => $date,
                    'formatted' => $formattedDate,
                    'include_time' => $includeTime,
                    'locale' => $locale ?? App::getLocale()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to format date',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vessel-specific locale
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getVesselLocale(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'vessel_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $vesselId = $request->input('vessel_id');
            $vesselLocale = LocalizationService::getVesselLocale($vesselId);

            return response()->json([
                'success' => true,
                'data' => [
                    'vessel_id' => $vesselId,
                    'locale' => $vesselLocale,
                    'has_specific_locale' => !is_null($vesselLocale)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vessel locale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report locale preferences
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getReportLocale(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'report_type' => 'sometimes|string|in:dvr,dcr,general'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reportType = $request->input('report_type', 'general');
            $reportLocale = LocalizationService::getReportLocale($reportType);

            return response()->json([
                'success' => true,
                'data' => [
                    'report_type' => $reportType,
                    'locale' => $reportLocale,
                    'locales' => [
                        'dvr' => LocalizationService::getReportLocale('dvr'),
                        'dcr' => LocalizationService::getReportLocale('dcr'),
                        'general' => LocalizationService::getReportLocale('general')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get report locale',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
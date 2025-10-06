<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for LocalizationService
 * 
 * Provides easy access to localization functionality throughout the application
 * 
 * @method static array getSupportedLocales()
 * @method static bool isValidLocale(string $locale)
 * @method static string getActivityMessage(string $key, array $params = [], string $locale = null)
 * @method static string|null getUserLocale(int $userId = null)
 * @method static bool setUserLocale(string $locale, int $userId = null)
 * @method static string formatNumber(float $number, int $decimals = 2, string $locale = null)
 * @method static string formatCurrency(float $amount, string $locale = null)
 * @method static string formatDate(string|\DateTime $date, string $locale = null, bool $includeTime = false)
 * @method static array getCurrentLocaleInfo()
 * @method static string|null getVesselLocale(int $vesselId)
 * @method static string getReportLocale(string $reportType = 'general')
 * @method static string getActivityLogLocale()
 * @method static array getLocaleSettings(string $locale)
 * @method static string parseAcceptLanguage(string $acceptLanguage)
 * @method static bool validateLocaleData(array $data)
 * @method static array getDateTimeFormats(string $locale = null)
 * @method static array getNumberFormats(string $locale = null)
 * @method static string getLocalizedModelName(string $modelClass, string $locale = null)
 * @method static array getActivityLogMessages(string $locale = null)
 * 
 * @see \App\Services\LocalizationService
 */
class Localization extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'localization';
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class LocalizationService
{
    /**
     * Get localized activity message
     *
     * @param string $key Translation key
     * @param array $params Parameters for translation
     * @param string|null $locale Override locale
     * @return string
     */
    public static function getActivityMessage(string $key, array $params = [], ?string $locale = null): string
    {
        $originalLocale = App::getLocale();
        
        if ($locale && $locale !== $originalLocale) {
            App::setLocale($locale);
        }
        
        $message = __($key, $params);
        
        // Restore original locale if changed
        if ($locale && $locale !== $originalLocale) {
            App::setLocale($originalLocale);
        }
        
        return $message;
    }

    /**
     * Get user's preferred locale
     *
     * @return string
     */
    public static function getUserLocale(): string
    {
        // Check authenticated user's preference
        if (Auth::check() && method_exists(Auth::user(), 'getLocale') && Auth::user()->getLocale()) {
            return Auth::user()->getLocale();
        }
        
        // Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }
        
        // Default locale
        return Config::get('localization.locale', 'en');
    }

    /**
     * Set user's preferred locale
     *
     * @param string $locale
     * @return bool
     */
    public static function setUserLocale(string $locale): bool
    {
        if (!self::isValidLocale($locale)) {
            return false;
        }
        
        // Set application locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Update user's preference if authenticated
        if (Auth::check() && method_exists(Auth::user(), 'setLocale')) {
            Auth::user()->setLocale($locale);
            Auth::user()->save();
        }
        
        return true;
    }

    /**
     * Check if locale is valid/supported
     *
     * @param string $locale
     * @return bool
     */
    public static function isValidLocale(string $locale): bool
    {
        $supportedLocales = array_keys(Config::get('localization.supported_locales', []));
        return in_array($locale, $supportedLocales);
    }

    /**
     * Get all supported locales
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return Config::get('localization.supported_locales', []);
    }

    /**
     * Format number according to locale
     *
     * @param float|int $number
     * @param int $decimals
     * @param string|null $locale
     * @return string
     */
    public static function formatNumber($number, int $decimals = 2, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $format = Config::get("localization.number_format.{$locale}", Config::get('localization.number_format.en'));
        
        return number_format(
            $number,
            $decimals,
            $format['decimal_separator'],
            $format['thousands_separator']
        );
    }

    /**
     * Format currency according to locale
     *
     * @param float|int $amount
     * @param string|null $locale
     * @return string
     */
    public static function formatCurrency($amount, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $format = Config::get("localization.number_format.{$locale}", Config::get('localization.number_format.en'));
        
        $formattedAmount = self::formatNumber($amount, 2, $locale);
        
        if ($format['currency_position'] === 'before') {
            return $format['currency_symbol'] . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $format['currency_symbol'];
        }
    }

    /**
     * Format date according to locale
     *
     * @param Carbon|string $date
     * @param string|null $locale
     * @param bool $includeTime
     * @return string
     */
    public static function formatDate($date, ?string $locale = null, bool $includeTime = false): string
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        $locale = $locale ?? App::getLocale();
        $format = Config::get("localization.datetime.{$locale}", Config::get('localization.datetime.en'));
        
        if ($includeTime) {
            return $date->format($format['datetime_format']);
        } else {
            return $date->format($format['date_format']);
        }
    }

    /**
     * Format time according to locale
     *
     * @param Carbon|string $time
     * @param string|null $locale
     * @return string
     */
    public static function formatTime($time, ?string $locale = null): string
    {
        if (!$time instanceof Carbon) {
            $time = Carbon::parse($time);
        }
        
        $locale = $locale ?? App::getLocale();
        $format = Config::get("localization.datetime.{$locale}", Config::get('localization.datetime.en'));
        
        return $time->format($format['time_format']);
    }

    /**
     * Get vessel-specific locale
     *
     * @param int $vesselId
     * @return string|null
     */
    public static function getVesselLocale(int $vesselId): ?string
    {
        $vesselLocales = Config::get('localization.vessel_locales', []);
        return $vesselLocales[$vesselId] ?? null;
    }

    /**
     * Set vessel-specific locale
     *
     * @param int $vesselId
     * @param string $locale
     * @return bool
     */
    public static function setVesselLocale(int $vesselId, string $locale): bool
    {
        if (!self::isValidLocale($locale)) {
            return false;
        }
        
        // This would typically update a database or config file
        // For now, we'll just update the runtime config
        $vesselLocales = Config::get('localization.vessel_locales', []);
        $vesselLocales[$vesselId] = $locale;
        Config::set('localization.vessel_locales', $vesselLocales);
        
        return true;
    }

    /**
     * Get activity log locale preference
     *
     * @return string
     */
    public static function getActivityLogLocale(): string
    {
        $config = Config::get('localization.activity_log');
        
        // Use user locale if configured
        if ($config['use_user_locale']) {
            $userLocale = self::getUserLocale();
            if (self::isValidLocale($userLocale)) {
                return $userLocale;
            }
        }
        
        // Fallback to system default if configured
        if ($config['fallback_to_system']) {
            return App::getLocale();
        }
        
        // Use activity log default
        return $config['default_locale'];
    }

    /**
     * Get report locale preference
     *
     * @param string $reportType (dvr, dcr, or general)
     * @return string
     */
    public static function getReportLocale(string $reportType = 'general'): string
    {
        $config = Config::get('localization.reports');
        
        // Check specific report type locale
        if ($reportType === 'dvr' && isset($config['dvr_locale'])) {
            return $config['dvr_locale'];
        }
        
        if ($reportType === 'dcr' && isset($config['dcr_locale'])) {
            return $config['dcr_locale'];
        }
        
        // Use user locale if allowed
        if ($config['allow_user_selection']) {
            $userLocale = self::getUserLocale();
            if (self::isValidLocale($userLocale)) {
                return $userLocale;
            }
        }
        
        // Default report locale
        return $config['default_locale'];
    }

    /**
     * Get localized validation messages
     *
     * @param string $rule
     * @param string $attribute
     * @param array $params
     * @param string|null $locale
     * @return string
     */
    public static function getValidationMessage(string $rule, string $attribute, array $params = [], ?string $locale = null): string
    {
        $originalLocale = App::getLocale();
        
        if ($locale && $locale !== $originalLocale) {
            App::setLocale($locale);
        }
        
        $message = __('validation.' . $rule, array_merge(['attribute' => $attribute], $params));
        
        // Restore original locale if changed
        if ($locale && $locale !== $originalLocale) {
            App::setLocale($originalLocale);
        }
        
        return $message;
    }

    /**
     * Get current locale information
     *
     * @return array
     */
    public static function getCurrentLocaleInfo(): array
    {
        $currentLocale = App::getLocale();
        $supportedLocales = self::getSupportedLocales();
        
        return $supportedLocales[$currentLocale] ?? [
            'name' => 'Unknown',
            'native' => 'Unknown',
            'flag' => 'unknown',
            'rtl' => false,
        ];
    }
}
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | This value is the default locale that will be used by the translation
    | service provider. You are free to set this value to any of the locales
    | which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | This array contains all the locales that your application supports.
    | These locales will be available for users to select from.
    |
    */

    'supported_locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => 'us',
            'rtl' => false,
        ],
        'id' => [
            'name' => 'Indonesian',
            'native' => 'Bahasa Indonesia',
            'flag' => 'id',
            'rtl' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Locale Settings
    |--------------------------------------------------------------------------
    |
    | These settings control how activity logs are localized throughout
    | the NEPTUNE system. You can set different locales for different
    | types of activities or users.
    |
    */

    'activity_log' => [
        'default_locale' => env('ACTIVITY_LOG_LOCALE', 'en'),
        'use_user_locale' => env('ACTIVITY_LOG_USE_USER_LOCALE', true),
        'fallback_to_system' => env('ACTIVITY_LOG_FALLBACK_TO_SYSTEM', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Locale Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the localization of generated reports like
    | DVR and DCR. Reports can be generated in different languages based
    | on user preferences or vessel requirements.
    |
    */

    'reports' => [
        'default_locale' => env('REPORT_LOCALE', 'en'),
        'dvr_locale' => env('DVR_LOCALE', 'en'),
        'dcr_locale' => env('DCR_LOCALE', 'en'),
        'allow_user_selection' => env('REPORT_ALLOW_USER_LOCALE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Date and Time Localization
    |--------------------------------------------------------------------------
    |
    | Configure how dates and times are displayed in different locales.
    | This affects activity logs, reports, and UI components.
    |
    */

    'datetime' => [
        'en' => [
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'datetime_format' => 'Y-m-d H:i:s',
            'timezone_display' => 'UTC',
        ],
        'id' => [
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i:s',
            'datetime_format' => 'd/m/Y H:i:s',
            'timezone_display' => 'WIB',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Number Formatting
    |--------------------------------------------------------------------------
    |
    | Configure how numbers are formatted in different locales.
    | This affects production data, equipment readings, and calculations.
    |
    */

    'number_format' => [
        'en' => [
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => '$',
            'currency_position' => 'before', // before or after
        ],
        'id' => [
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'Rp',
            'currency_position' => 'before',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vessel-Specific Locale Settings
    |--------------------------------------------------------------------------
    |
    | Some vessels may require specific locales for their operations.
    | This allows you to override the default locale per vessel.
    |
    */

    'vessel_locales' => [
        // Example: 'vessel_id' => 'locale_code'
        // 1 => 'id', // Indonesian vessel
        // 2 => 'en', // International vessel
    ],

];
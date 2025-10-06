<?php

namespace App\Providers;

use App\Services\LocalizationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register LocalizationService as singleton
        $this->app->singleton(LocalizationService::class, function ($app) {
            return new LocalizationService();
        });

        // Register alias for easier access
        $this->app->alias(LocalizationService::class, 'localization');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load localization configuration
        $this->loadLocalizationConfig();

        // Set up locale detection and fallback
        $this->setupLocaleDetection();

        // Share localization data with views
        $this->shareLocalizationWithViews();

        // Register custom validation rules
        $this->registerValidationRules();

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Load localization configuration
     */
    protected function loadLocalizationConfig(): void
    {
        // Merge localization config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/localization.php',
            'localization'
        );

        // Set supported locales
        $supportedLocales = Config::get('localization.supported_locales', ['en', 'id']);
        Config::set('app.supported_locales', $supportedLocales);

        // Set fallback locale
        $fallbackLocale = Config::get('localization.fallback_locale', 'en');
        Config::set('app.fallback_locale', $fallbackLocale);
    }

    /**
     * Setup locale detection and fallback
     */
    protected function setupLocaleDetection(): void
    {
        // Get default locale from config
        $defaultLocale = Config::get('localization.default_locale', 'en');
        
        // Set application locale
        App::setLocale($defaultLocale);
        
        // Set fallback locale
        $fallbackLocale = Config::get('localization.fallback_locale', 'en');
        App::setFallbackLocale($fallbackLocale);
    }

    /**
     * Share localization data with views
     */
    protected function shareLocalizationWithViews(): void
    {
        View::composer('*', function ($view) {
            $localizationService = app(LocalizationService::class);
            
            $view->with([
                'currentLocale' => App::getLocale(),
                'supportedLocales' => $localizationService->getSupportedLocales(),
                'localeInfo' => $localizationService->getCurrentLocaleInfo(),
            ]);
        });
    }

    /**
     * Register custom validation rules
     */
    protected function registerValidationRules(): void
    {
        // Validate locale code
        \Illuminate\Support\Facades\Validator::extend('locale', function ($attribute, $value, $parameters, $validator) {
            $localizationService = app(LocalizationService::class);
            return $localizationService->isValidLocale($value);
        });

        // Custom validation message
        \Illuminate\Support\Facades\Validator::replacer('locale', function ($message, $attribute, $rule, $parameters) {
            $localizationService = app(LocalizationService::class);
            $supportedLocales = array_keys($localizationService->getSupportedLocales());
            
            return str_replace(':attribute', $attribute, 
                "The :attribute must be a valid locale code. Supported locales: " . implode(', ', $supportedLocales)
            );
        });
    }

    /**
     * Register Blade directives for localization
     */
    protected function registerBladeDirectives(): void
    {
        // @locale directive - check current locale
        \Illuminate\Support\Facades\Blade::directive('locale', function ($expression) {
            return "<?php if(app()->getLocale() === {$expression}): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endlocale', function () {
            return "<?php endif; ?>";
        });

        // @notlocale directive - check if not current locale
        \Illuminate\Support\Facades\Blade::directive('notlocale', function ($expression) {
            return "<?php if(app()->getLocale() !== {$expression}): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endnotlocale', function () {
            return "<?php endif; ?>";
        });

        // @formatnumber directive - format number according to locale
        \Illuminate\Support\Facades\Blade::directive('formatnumber', function ($expression) {
            return "<?php echo app('localization')->formatNumber({$expression}); ?>";
        });

        // @formatcurrency directive - format currency according to locale
        \Illuminate\Support\Facades\Blade::directive('formatcurrency', function ($expression) {
            return "<?php echo app('localization')->formatCurrency({$expression}); ?>";
        });

        // @formatdate directive - format date according to locale
        \Illuminate\Support\Facades\Blade::directive('formatdate', function ($expression) {
            return "<?php echo app('localization')->formatDate({$expression}); ?>";
        });

        // @activitymessage directive - get localized activity message
        \Illuminate\Support\Facades\Blade::directive('activitymessage', function ($expression) {
            return "<?php echo app('localization')->getActivityMessage({$expression}); ?>";
        });

        // @localeflag directive - get locale flag
        \Illuminate\Support\Facades\Blade::directive('localeflag', function ($expression) {
            $expression = $expression ?: 'app()->getLocale()';
            return "<?php \$locales = app('localization')->getSupportedLocales(); echo \$locales[{$expression}]['flag'] ?? '🌐'; ?>";
        });

        // @localename directive - get locale display name
        \Illuminate\Support\Facades\Blade::directive('localename', function ($expression) {
            $expression = $expression ?: 'app()->getLocale()';
            return "<?php \$locales = app('localization')->getSupportedLocales(); echo \$locales[{$expression}]['name'] ?? {$expression}; ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            LocalizationService::class,
            'localization',
        ];
    }
}
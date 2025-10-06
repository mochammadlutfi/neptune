<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);
        
        // Validate locale is supported
        if ($this->isValidLocale($locale)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Fallback to default locale
            $fallbackLocale = Config::get('localization.fallback_locale', 'en');
            App::setLocale($fallbackLocale);
            Session::put('locale', $fallbackLocale);
        }

        return $next($request);
    }

    /**
     * Determine the appropriate locale for the request
     * Priority: URL parameter > User preference > Session > Cookie > Accept-Language header > Default
     *
     * @param Request $request
     * @return string
     */
    private function determineLocale(Request $request): string
    {
        // 1. Check URL parameter (highest priority)
        if ($request->has('locale')) {
            $urlLocale = $request->get('locale');
            if ($this->isValidLocale($urlLocale)) {
                return $urlLocale;
            }
        }

        // 2. Check authenticated user's preference
        if (Auth::check() && method_exists(Auth::user(), 'getLocale') && Auth::user()->getLocale()) {
            $userLocale = Auth::user()->getLocale();
            if ($this->isValidLocale($userLocale)) {
                return $userLocale;
            }
        }

        // 3. Check session
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if ($this->isValidLocale($sessionLocale)) {
                return $sessionLocale;
            }
        }

        // 4. Check cookie (legacy support)
        if ($request->cookie('locale')) {
            $cookieLocale = $request->cookie('locale');
            if ($this->isValidLocale($cookieLocale)) {
                return $cookieLocale;
            }
        }

        // 5. Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLocale = $this->parseAcceptLanguage($acceptLanguage);
            if ($this->isValidLocale($preferredLocale)) {
                return $preferredLocale;
            }
        }

        // 6. Check vessel-specific locale if user has vessel context
        if (Auth::check() && method_exists(Auth::user(), 'getCurrentVesselId') && Auth::user()->getCurrentVesselId()) {
            $vesselLocales = Config::get('localization.vessel_locales', []);
            $vesselId = Auth::user()->getCurrentVesselId();
            
            if (isset($vesselLocales[$vesselId])) {
                $vesselLocale = $vesselLocales[$vesselId];
                if ($this->isValidLocale($vesselLocale)) {
                    return $vesselLocale;
                }
            }
        }

        // 7. Default locale
        return Config::get('localization.locale', 'en');
    }

    /**
     * Check if the given locale is valid/supported
     *
     * @param string $locale
     * @return bool
     */
    private function isValidLocale(string $locale): bool
    {
        $supportedLocales = array_keys(Config::get('localization.supported_locales', []));
        return in_array($locale, $supportedLocales);
    }

    /**
     * Parse Accept-Language header and return the most preferred supported locale
     *
     * @param string $acceptLanguage
     * @return string|null
     */
    private function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $languages = [];
        
        // Parse Accept-Language header
        // Format: en-US,en;q=0.9,id;q=0.8
        preg_match_all('/([a-z]{2}(?:-[A-Z]{2})?)(?:;q=([0-9.]+))?/', $acceptLanguage, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $lang = strtolower(substr($match[1], 0, 2)); // Extract language code (en from en-US)
            $quality = isset($match[2]) ? (float) $match[2] : 1.0;
            $languages[$lang] = $quality;
        }
        
        // Sort by quality (preference)
        arsort($languages);
        
        // Find the first supported language
        foreach (array_keys($languages) as $lang) {
            if ($this->isValidLocale($lang)) {
                return $lang;
            }
        }
        
        return null;
    }

    /**
     * Get current locale information
     *
     * @return array
     */
    public static function getCurrentLocaleInfo(): array
    {
        $currentLocale = App::getLocale();
        $supportedLocales = Config::get('localization.supported_locales', []);
        
        return $supportedLocales[$currentLocale] ?? [
            'name' => 'Unknown',
            'native' => 'Unknown',
            'flag' => 'unknown',
            'rtl' => false,
        ];
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
}

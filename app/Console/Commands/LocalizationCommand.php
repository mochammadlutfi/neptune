<?php

namespace App\Console\Commands;

use App\Services\LocalizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class LocalizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'localization:manage 
                            {action : The action to perform (publish|clear-cache|sync|validate|export|import)}
                            {--locale= : Specific locale to work with}
                            {--force : Force the operation}
                            {--format=json : Export/import format (json|php|csv)}';

    /**
     * The console command description.
     */
    protected $description = 'Manage NEPTUNE localization system';

    /**
     * LocalizationService instance
     */
    protected LocalizationService $localizationService;

    /**
     * Create a new command instance.
     */
    public function __construct(LocalizationService $localizationService)
    {
        parent::__construct();
        $this->localizationService = $localizationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'publish':
                return $this->publishLocaleFiles();
            case 'clear-cache':
                return $this->clearLocalizationCache();
            case 'sync':
                return $this->syncTranslations();
            case 'validate':
                return $this->validateTranslations();
            case 'export':
                return $this->exportTranslations();
            case 'import':
                return $this->importTranslations();
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: publish, clear-cache, sync, validate, export, import');
                return 1;
        }
    }

    /**
     * Publish locale files to lang directory
     */
    protected function publishLocaleFiles(): int
    {
        $this->info('Publishing NEPTUNE localization files...');

        $supportedLocales = Config::get('localization.supported_locales', ['en', 'id']);
        $langPath = resource_path('lang');

        // Ensure lang directory exists
        if (!File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);
            $this->info("Created lang directory: {$langPath}");
        }

        $published = 0;
        foreach ($supportedLocales as $locale => $config) {
            $localeCode = is_array($config) ? $locale : $config;
            $localePath = $langPath . DIRECTORY_SEPARATOR . $localeCode;

            // Create locale directory
            if (!File::exists($localePath)) {
                File::makeDirectory($localePath, 0755, true);
                $this->info("Created locale directory: {$localePath}");
            }

            // Publish activity.php if it doesn't exist or force is used
            $activityFile = $localePath . DIRECTORY_SEPARATOR . 'activity.php';
            if (!File::exists($activityFile) || $this->option('force')) {
                $this->publishActivityFile($localeCode, $activityFile);
                $published++;
            }

            // Publish other locale files as needed
            $this->publishCommonFiles($localeCode, $localePath);
        }

        $this->info("Published {$published} locale files.");
        return 0;
    }

    /**
     * Publish activity.php file for a locale
     */
    protected function publishActivityFile(string $locale, string $filePath): void
    {
        $template = $this->getActivityFileTemplate($locale);
        File::put($filePath, $template);
        $this->info("Published activity.php for locale: {$locale}");
    }

    /**
     * Get activity file template for a locale
     */
    protected function getActivityFileTemplate(string $locale): string
    {
        $isIndonesian = $locale === 'id';
        
        return "<?php\n\nreturn [\n" .
            "    // General activity messages\n" .
            "    'general' => [\n" .
            "        'created' => '" . ($isIndonesian ? 'Membuat :model' : 'Created :model') . "',\n" .
            "        'updated' => '" . ($isIndonesian ? 'Mengubah :model' : 'Updated :model') . "',\n" .
            "        'deleted' => '" . ($isIndonesian ? 'Menghapus :model' : 'Deleted :model') . "',\n" .
            "        'viewed' => '" . ($isIndonesian ? 'Melihat :model' : 'Viewed :model') . "',\n" .
            "        'exported' => '" . ($isIndonesian ? 'Mengekspor :model' : 'Exported :model') . "',\n" .
            "        'imported' => '" . ($isIndonesian ? 'Mengimpor :model' : 'Imported :model') . "',\n" .
            "    ],\n\n" .
            "    // Well Production activities\n" .
            "    'well_production' => [\n" .
            "        'created' => '" . ($isIndonesian ? 'Menambahkan data produksi sumur :well_name' : 'Added production data for well :well_name') . "',\n" .
            "        'updated' => '" . ($isIndonesian ? 'Mengubah data produksi sumur :well_name' : 'Updated production data for well :well_name') . "',\n" .
            "        'deleted' => '" . ($isIndonesian ? 'Menghapus data produksi sumur :well_name' : 'Deleted production data for well :well_name') . "',\n" .
            "    ],\n" .
            "];\n";
    }

    /**
     * Publish common locale files
     */
    protected function publishCommonFiles(string $locale, string $localePath): void
    {
        $commonFiles = ['validation', 'passwords', 'pagination'];
        
        foreach ($commonFiles as $file) {
            $filePath = $localePath . DIRECTORY_SEPARATOR . $file . '.php';
            if (!File::exists($filePath)) {
                // Create basic template for common files
                $template = "<?php\n\nreturn [\n    // {$file} messages for {$locale}\n];\n";
                File::put($filePath, $template);
                $this->info("Created {$file}.php for locale: {$locale}");
            }
        }
    }

    /**
     * Clear localization cache
     */
    protected function clearLocalizationCache(): int
    {
        $this->info('Clearing localization cache...');

        // Clear Laravel translation cache
        $this->call('config:clear');
        $this->call('cache:clear');

        // Clear custom localization cache
        $cacheKeys = [
            'localization.supported_locales',
            'localization.activity_messages',
            'localization.user_locales',
            'localization.vessel_locales',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        $this->info('Localization cache cleared successfully.');
        return 0;
    }

    /**
     * Sync translations between locales
     */
    protected function syncTranslations(): int
    {
        $this->info('Syncing translations...');

        $sourceLocale = $this->option('locale') ?? 'en';
        $supportedLocales = array_keys(Config::get('localization.supported_locales', ['en', 'id']));

        if (!in_array($sourceLocale, $supportedLocales)) {
            $this->error("Source locale '{$sourceLocale}' is not supported.");
            return 1;
        }

        $sourcePath = resource_path("lang/{$sourceLocale}");
        if (!File::exists($sourcePath)) {
            $this->error("Source locale directory does not exist: {$sourcePath}");
            return 1;
        }

        $sourceFiles = File::files($sourcePath);
        $synced = 0;

        foreach ($supportedLocales as $targetLocale) {
            if ($targetLocale === $sourceLocale) {
                continue;
            }

            $targetPath = resource_path("lang/{$targetLocale}");
            if (!File::exists($targetPath)) {
                File::makeDirectory($targetPath, 0755, true);
            }

            foreach ($sourceFiles as $file) {
                $fileName = $file->getFilename();
                $targetFile = $targetPath . DIRECTORY_SEPARATOR . $fileName;

                if (!File::exists($targetFile) || $this->option('force')) {
                    $sourceContent = include $file->getPathname();
                    $this->syncFileContent($sourceContent, $targetFile, $targetLocale);
                    $synced++;
                }
            }
        }

        $this->info("Synced {$synced} translation files.");
        return 0;
    }

    /**
     * Sync file content to target locale
     */
    protected function syncFileContent(array $content, string $targetFile, string $targetLocale): void
    {
        $existingContent = File::exists($targetFile) ? include $targetFile : [];
        $mergedContent = array_merge($content, $existingContent);

        $exportContent = "<?php\n\nreturn " . var_export($mergedContent, true) . ";\n";
        File::put($targetFile, $exportContent);

        $this->info("Synced: {$targetFile}");
    }

    /**
     * Validate translations
     */
    protected function validateTranslations(): int
    {
        $this->info('Validating translations...');

        $supportedLocales = array_keys(Config::get('localization.supported_locales', ['en', 'id']));
        $errors = [];

        foreach ($supportedLocales as $locale) {
            $localePath = resource_path("lang/{$locale}");
            
            if (!File::exists($localePath)) {
                $errors[] = "Missing locale directory: {$localePath}";
                continue;
            }

            $files = File::files($localePath);
            foreach ($files as $file) {
                try {
                    $content = include $file->getPathname();
                    if (!is_array($content)) {
                        $errors[] = "Invalid content in {$file->getPathname()}: must return array";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error loading {$file->getPathname()}: {$e->getMessage()}";
                }
            }
        }

        if (empty($errors)) {
            $this->info('All translations are valid.');
            return 0;
        } else {
            $this->error('Validation errors found:');
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }
    }

    /**
     * Export translations
     */
    protected function exportTranslations(): int
    {
        $this->info('Exporting translations...');
        
        $format = $this->option('format');
        $locale = $this->option('locale');
        
        // Implementation for export functionality
        $this->info("Export functionality will be implemented for format: {$format}");
        
        return 0;
    }

    /**
     * Import translations
     */
    protected function importTranslations(): int
    {
        $this->info('Importing translations...');
        
        $format = $this->option('format');
        $locale = $this->option('locale');
        
        // Implementation for import functionality
        $this->info("Import functionality will be implemented for format: {$format}");
        
        return 0;
    }
}
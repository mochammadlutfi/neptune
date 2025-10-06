<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', 'Lova POS');
        $this->migrator->add('general.app_version', '1.0.0');
        $this->migrator->add('general.company_name', "Lova Code");
        $this->migrator->add('general.company_email', "lovacode@gmail.com");
        $this->migrator->add('general.company_phone', "6213214123");
        $this->migrator->add('general.company_address', "Road Runner 21");
        $this->migrator->add('general.favicon', '/images/logo/favicon.png');
        $this->migrator->add('general.logo_light', '/images/logo/logo-light.png');
        $this->migrator->add('general.logo_dark', '/images/logo/logo-dark.png');
        $this->migrator->add('general.logo_light_sm', '/images/logo/logo-sm.png');
        $this->migrator->add('general.logo_dark_sm', '/images/logo/logo-sm.png');
        $this->migrator->add('general.locale', 'en');
        $this->migrator->add('general.branch', 1);
    }

    
    public function down(): void
    {
        $this->migrator->delete('general.app_name');
        $this->migrator->delete('general.app_version');
        $this->migrator->delete('general.company_name');
        $this->migrator->delete('general.company_email');
        $this->migrator->delete('general.company_phone');
        $this->migrator->delete('general.company_address');
        $this->migrator->delete('general.favicon');
        $this->migrator->delete('general.logo_light');
        $this->migrator->delete('general.logo_dark');
        $this->migrator->delete('general.logo_light_sm');
        $this->migrator->delete('general.logo_dark_sm');
        $this->migrator->delete('general.locale');
        $this->migrator->delete('general.branch');
    }
};

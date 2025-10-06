<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Commands\Seed;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class ActivityLogMenuSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk menambahkan menu Activity Log.
     * Menu ini akan ditambahkan ke dalam grup Settings.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Cari menu Settings (parent)
            $settingsMenu = Menu::where('name', 'base.setting')->first();
            
            if (!$settingsMenu) {
                $this->command->error('Menu Settings tidak ditemukan. Pastikan seeder menu utama sudah dijalankan.');
                return;
            }

            // Cek apakah menu Activity Log sudah ada
            $existingMenu = Menu::where('name', 'activity.log.title')
                ->where('parent_id', $settingsMenu->id)
                ->first();

            if ($existingMenu) {
                $this->command->info('Menu Activity Log sudah ada.');
                return;
            }

            // Dapatkan order terakhir dari submenu Settings
            $lastOrder = Menu::where('parent_id', $settingsMenu->id)
                ->max('order') ?? 0;

            // Buat menu Activity Log
            $activityLogMenu = Menu::create([
                'parent_id' => $settingsMenu->id,
                'name' => 'activity.log.title',
                'icon' => null, // Icon akan diatur di parent menu
                'to' => '/settings/activity-log',
                'permission' => 'activity_log.view',
                'module' => 'core',
                'order' => $lastOrder + 10,
            ]);

            $this->command->info('Menu Activity Log berhasil ditambahkan dengan ID: ' . $activityLogMenu->id);
        });
    }

    /**
     * Menghapus menu Activity Log (untuk rollback).
     */
    public function rollback(): void
    {
        DB::transaction(function () {
            $activityLogMenu = Menu::where('name', 'activity.log.title')->first();
            
            if ($activityLogMenu) {
                $activityLogMenu->delete();
                $this->command->info('Menu Activity Log berhasil dihapus.');
            } else {
                $this->command->info('Menu Activity Log tidak ditemukan.');
            }
        });
    }
}
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Log Language Lines
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk pesan activity logging
    | di seluruh aplikasi. Anda bebas memodifikasi baris bahasa ini
    | sesuai dengan kebutuhan aplikasi Anda.
    |
    */

    'well_production' => [
        'created' => 'Membuat well production reading untuk :well_name',
        'updated' => 'Mengupdate well production reading untuk :well_name',
        'deleted' => 'Menghapus well production reading untuk :well_name',
    ],

    'equipment' => [
        'created' => 'Membuat record equipment untuk :equipment_name',
        'updated' => 'Mengupdate record equipment untuk :equipment_name',
        'deleted' => 'Menghapus record equipment untuk :equipment_name',
    ],

    'equipment_status' => [
        'created' => 'Membuat pembacaan status equipment untuk :equipment_name',
        'updated' => 'Mengupdate pembacaan status equipment untuk :equipment_name',
        'deleted' => 'Menghapus pembacaan status equipment untuk :equipment_name',
    ],

    'hse' => [
        'incident_created' => 'Membuat laporan insiden HSE: :incident_type',
        'incident_updated' => 'Mengupdate laporan insiden HSE: :incident_type',
        'incident_deleted' => 'Menghapus laporan insiden HSE: :incident_type',
        'pob_created' => 'Membuat record POB untuk :vessel_name',
        'pob_updated' => 'Mengupdate record POB untuk :vessel_name',
        'pob_deleted' => 'Menghapus record POB untuk :vessel_name',
    ],

    'marine' => [
        'ballast_created' => 'Membuat record operasi ballast',
        'ballast_updated' => 'Mengupdate record operasi ballast',
        'ballast_deleted' => 'Menghapus record operasi ballast',
        'weather_created' => 'Membuat record observasi cuaca',
        'weather_updated' => 'Mengupdate record observasi cuaca',
        'weather_deleted' => 'Menghapus record observasi cuaca',
    ],

    'fuel' => [
        'consumption_created' => 'Membuat record konsumsi bahan bakar untuk :fuel_type',
        'consumption_updated' => 'Mengupdate record konsumsi bahan bakar untuk :fuel_type',
        'consumption_deleted' => 'Menghapus record konsumsi bahan bakar untuk :fuel_type',
        'transfer_created' => 'Membuat record transfer bahan bakar',
        'transfer_updated' => 'Mengupdate record transfer bahan bakar',
        'transfer_deleted' => 'Menghapus record transfer bahan bakar',
    ],

    'flare' => [
        'created' => 'Membuat record operasi flare',
        'updated' => 'Mengupdate record operasi flare',
        'deleted' => 'Menghapus record operasi flare',
    ],

    'master_data' => [
        'vessel_created' => 'Membuat vessel: :vessel_name',
        'vessel_updated' => 'Mengupdate vessel: :vessel_name',
        'vessel_deleted' => 'Menghapus vessel: :vessel_name',
        'well_created' => 'Membuat well: :well_name',
        'well_updated' => 'Mengupdate well: :well_name',
        'well_deleted' => 'Menghapus well: :well_name',
        'equipment_created' => 'Membuat equipment: :equipment_name',
        'equipment_updated' => 'Mengupdate equipment: :equipment_name',
        'equipment_deleted' => 'Menghapus equipment: :equipment_name',
    ],

    'user_management' => [
        'user_created' => 'Membuat akun user: :username',
        'user_updated' => 'Mengupdate akun user: :username',
        'user_deleted' => 'Menghapus akun user: :username',
        'role_assigned' => 'Memberikan role :role_name kepada user :username',
        'role_removed' => 'Menghapus role :role_name dari user :username',
    ],

    'authentication' => [
        'login' => 'User berhasil login',
        'logout' => 'User berhasil logout',
        'password_changed' => 'Password berhasil diubah',
        'profile_updated' => 'Profil berhasil diupdate',
    ],

    'reports' => [
        'dvr_generated' => 'Generate laporan DVR untuk tanggal :date',
        'dcr_generated' => 'Generate laporan DCR untuk tanggal :date',
        'custom_report_generated' => 'Generate laporan custom: :report_name',
    ],

    'system' => [
        'backup_created' => 'Backup sistem berhasil dibuat',
        'backup_failed' => 'Backup sistem gagal',
        'maintenance_started' => 'Maintenance sistem dimulai',
        'maintenance_completed' => 'Maintenance sistem selesai',
        'cache_cleared' => 'Cache sistem dibersihkan',
        'logs_rotated' => 'Log sistem dirotasi',
    ],

    // General activities (fallback messages)
    'general' => [
        'created' => 'Membuat :model baru',
        'updated' => 'Mengupdate :model',
        'deleted' => 'Menghapus :model',
        'viewed' => 'Melihat :model',
        'exported' => 'Mengekspor data :model',
        'imported' => 'Mengimpor data :model',
    ],
];
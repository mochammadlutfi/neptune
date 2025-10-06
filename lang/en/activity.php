<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Log Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for activity logging messages
    | throughout the application. You are free to modify these language lines
    | according to your application's requirements.
    |
    */

    'well_production' => [
        'created' => 'Created well production reading for :well_name',
        'updated' => 'Updated well production reading for :well_name',
        'deleted' => 'Deleted well production reading for :well_name',
    ],

    'equipment' => [
        'created' => 'Created equipment record for :equipment_name',
        'updated' => 'Updated equipment record for :equipment_name',
        'deleted' => 'Deleted equipment record for :equipment_name',
    ],

    'equipment_status' => [
        'created' => 'Created equipment status reading for :equipment_name',
        'updated' => 'Updated equipment status reading for :equipment_name',
        'deleted' => 'Deleted equipment status reading for :equipment_name',
    ],

    'hse' => [
        'incident_created' => 'Created HSE incident report: :incident_type',
        'incident_updated' => 'Updated HSE incident report: :incident_type',
        'incident_deleted' => 'Deleted HSE incident report: :incident_type',
        'pob_created' => 'Created POB record for :vessel_name',
        'pob_updated' => 'Updated POB record for :vessel_name',
        'pob_deleted' => 'Deleted POB record for :vessel_name',
    ],

    'marine' => [
        'ballast_created' => 'Created ballast operation record',
        'ballast_updated' => 'Updated ballast operation record',
        'ballast_deleted' => 'Deleted ballast operation record',
        'weather_created' => 'Created weather observation record',
        'weather_updated' => 'Updated weather observation record',
        'weather_deleted' => 'Deleted weather observation record',
    ],

    'fuel' => [
        'consumption_created' => 'Created fuel consumption record for :fuel_type',
        'consumption_updated' => 'Updated fuel consumption record for :fuel_type',
        'consumption_deleted' => 'Deleted fuel consumption record for :fuel_type',
        'transfer_created' => 'Created fuel transfer record',
        'transfer_updated' => 'Updated fuel transfer record',
        'transfer_deleted' => 'Deleted fuel transfer record',
    ],

    'flare' => [
        'created' => 'Created flare operation record',
        'updated' => 'Updated flare operation record',
        'deleted' => 'Deleted flare operation record',
    ],

    'master_data' => [
        'vessel_created' => 'Created vessel: :vessel_name',
        'vessel_updated' => 'Updated vessel: :vessel_name',
        'vessel_deleted' => 'Deleted vessel: :vessel_name',
        'well_created' => 'Created well: :well_name',
        'well_updated' => 'Updated well: :well_name',
        'well_deleted' => 'Deleted well: :well_name',
        'equipment_created' => 'Created equipment: :equipment_name',
        'equipment_updated' => 'Updated equipment: :equipment_name',
        'equipment_deleted' => 'Deleted equipment: :equipment_name',
    ],

    'user_management' => [
        'user_created' => 'Created user account: :username',
        'user_updated' => 'Updated user account: :username',
        'user_deleted' => 'Deleted user account: :username',
        'role_assigned' => 'Assigned role :role_name to user :username',
        'role_removed' => 'Removed role :role_name from user :username',
    ],

    'authentication' => [
        'login' => 'User logged in',
        'logout' => 'User logged out',
        'password_changed' => 'Password changed',
        'profile_updated' => 'Profile updated',
    ],

    'reports' => [
        'dvr_generated' => 'Generated DVR report for :date',
        'dcr_generated' => 'Generated DCR report for :date',
        'custom_report_generated' => 'Generated custom report: :report_name',
    ],

    'system' => [
        'backup_created' => 'System backup created',
        'maintenance_started' => 'System maintenance started',
        'maintenance_completed' => 'System maintenance completed',
        'settings_updated' => 'System settings updated',
    ],

    'general' => [
        'created' => 'Created new :model',
        'updated' => 'Updated :model',
        'deleted' => 'Deleted :model',
        'viewed' => 'Viewed :model',
        'exported' => 'Exported :model data',
        'imported' => 'Imported :model data',
    ],
];
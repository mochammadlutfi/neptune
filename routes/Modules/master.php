<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Master Data Routes
|--------------------------------------------------------------------------
|
| Routes untuk mengelola data master seperti vessels, wells, equipment,
| personnel positions, chemicals, dan fuel types.
|
*/

Route::namespace('Master')->prefix('/master')->name('master.')->group(function () {
    
    // Contracts
    Route::prefix('/contracts')->name('contracts.')->group(function () {
        Route::get('/', 'ContractController@index')->name('index');
        Route::post('/store', 'ContractController@store')->name('store');
        Route::get('/expiring', 'ContractController@getExpiringContracts')->name('expiring');
        Route::post('/validate-terms', 'ContractController@validateCommercialTerms')->name('validate_terms');
        Route::get('/{id}', 'ContractController@show')->name('show');
        Route::put('/{id}/update', 'ContractController@update')->name('update');
        Route::delete('/{id}/delete', 'ContractController@destroy')->name('delete');
        
        // Additional contract-specific routes
        Route::get('/{id}/performance', 'ContractController@getContractPerformance')->name('performance');
    });

    // Vessels
    Route::prefix('/vessels')->name('vessels.')->group(function () {
        Route::get('/', 'VesselController@index')->name('index');
        Route::post('/store', 'VesselController@store')->name('store');
        Route::get('/{id}', 'VesselController@show')->name('show');
        Route::put('/{id}/update', 'VesselController@update')->name('update');
        Route::delete('/{id}/delete', 'VesselController@destroy')->name('delete');
        Route::get('/{id}/wells', 'VesselController@wells')->name('wells');
        Route::get('/{id}/equipment', 'VesselController@equipment')->name('equipment');
    });

    // Wells
    Route::prefix('/wells')->name('wells.')->group(function () {
        Route::get('/', 'WellController@index')->name('index');
        Route::get('/stats', 'WellController@stats')->name('stats');
        Route::post('/store', 'WellController@store')->name('store');
        Route::get('/{id}', 'WellController@show')->name('show');
        Route::put('/{id}/update', 'WellController@update')->name('update');
        Route::put('/{id}/status', 'WellController@status')->name('status');
        Route::delete('/{id}/delete', 'WellController@destroy')->name('delete');
    });

    // Equipment
    Route::prefix('/equipment')->name('equipment.')->group(function () {
        Route::get('/', 'EquipmentController@index')->name('index');
        Route::post('/store', 'EquipmentController@store')->name('store');
        Route::get('/{id}', 'EquipmentController@show')->name('show');
        Route::put('/{id}/update', 'EquipmentController@update')->name('update');
        Route::delete('/{id}/delete', 'EquipmentController@destroy')->name('delete');
        Route::get('/critical', 'EquipmentController@critical')->name('critical');
    });

    // Personnel Positions
    Route::prefix('/personnel-positions')->name('personnel_positions.')->group(function () {
        Route::get('/', 'PersonnelPositionController@index')->name('index');
        Route::post('/store', 'PersonnelPositionController@store')->name('store');
        Route::get('/{id}', 'PersonnelPositionController@show')->name('show');
        Route::put('/{id}/update', 'PersonnelPositionController@update')->name('update');
        Route::delete('/{id}/delete', 'PersonnelPositionController@destroy')->name('delete');
    });

    // Chemicals
    Route::prefix('/chemicals')->name('chemicals.')->group(function () {
        Route::get('/', 'ChemicalController@index')->name('index');
        Route::post('/store', 'ChemicalController@store')->name('store');
        Route::get('/{id}', 'ChemicalController@show')->name('show');
        Route::put('/{id}/update', 'ChemicalController@update')->name('update');
        Route::delete('/{id}/delete', 'ChemicalController@destroy')->name('delete');
    });

    // Gas Buyer
    Route::prefix('/gas-buyer')->name('gas_buyer.')->group(function () {
        Route::get('/', 'GasBuyerController@index')->name('index');
        Route::post('/store', 'GasBuyerController@store')->name('store');
        Route::get('/{id}', 'GasBuyerController@show')->name('show');
        Route::put('/{id}/update', 'GasBuyerController@update')->name('update');
        Route::delete('/{id}/delete', 'GasBuyerController@destroy')->name('delete');
    });


    // Tank
    Route::prefix('/tank')->name('tank.')->group(function () {
        Route::get('/', 'TankController@index')->name('index');
        Route::post('/store', 'TankController@store')->name('store');
        Route::get('/{id}', 'TankController@show')->name('show');
        Route::put('/{id}/update', 'TankController@update')->name('update');
        Route::delete('/{id}/delete', 'TankController@destroy')->name('delete');
    });
});
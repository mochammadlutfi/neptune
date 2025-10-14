<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Production Operations Routes
|--------------------------------------------------------------------------
|
| Routes untuk mengelola operasi produksi seperti well production readings,
| gas sales metering, gas compression, dan production summary.
|
*/

Route::namespace('Production')->prefix('/production')->name('production.')->group(function () {
    
    // Production Statistics
    Route::prefix('/statistics')->name('statistics.')->group(function () {
        Route::get('/dashboard', 'ProductionStatisticsController@dashboard')->name('dashboard');
        Route::get('/wells-summary', 'ProductionStatisticsController@wellsSummary')->name('wells_summary');
        Route::get('/gas-sales-summary', 'ProductionStatisticsController@gasSalesSummary')->name('gas_sales_summary');
        Route::get('/compression-summary', 'ProductionStatisticsController@compressionSummary')->name('compression_summary');
        Route::get('/vessel/{vesselId}/summary', 'ProductionStatisticsController@vesselSummary')->name('vessel_summary');
        Route::get('/daily-summary/{date}', 'ProductionStatisticsController@dailySummary')->name('daily_summary');
        Route::get('/monthly-summary/{year}/{month}', 'ProductionStatisticsController@monthlySummary')->name('monthly_summary');
    });
    
    // Well Production Readings
    Route::prefix('/sales-gas-nomination')->name('sales-gas-nomination.')->group(function () {
        Route::get('/', 'SalesGasNominationController@index')->name('index');
        Route::post('/store', 'SalesGasNominationController@store')->name('store');
        Route::get('/{id}', 'SalesGasNominationController@show')->name('show');
        Route::put('/{id}/update', 'SalesGasNominationController@update')->name('update');
        Route::delete('/{id}/delete', 'SalesGasNominationController@destroy')->name('delete');
    });

    Route::prefix('/sales-gas-allocation')->name('sales-gas-allocation.')->group(function () {
        Route::get('/', 'SalesGasAllocationController@index')->name('index');
        Route::post('/store', 'SalesGasAllocationController@store')->name('store');
        Route::get('/{id}', 'SalesGasAllocationController@show')->name('show');
        Route::put('/{id}/update', 'SalesGasAllocationController@update')->name('update');
        Route::delete('/{id}/delete', 'SalesGasAllocationController@destroy')->name('delete');
    });

    Route::prefix('/sales-gas-metering')->name('sales-gas-metering.')->group(function () {
        Route::get('/', 'SalesGasMeteringController@index')->name('index');
        Route::get('/hourly-detail', 'SalesGasMeteringController@hourlyDetail')->name('hourlyDetail');
        Route::get('/daily-statistics', 'SalesGasMeteringController@dailyStatistics')->name('dailyStatistics');
        Route::post('/store', 'SalesGasMeteringController@store')->name('store');
        Route::get('/{id}', 'SalesGasMeteringController@show')->name('show');
        Route::put('/{id}/update', 'SalesGasMeteringController@update')->name('update');
        Route::delete('/{id}/delete', 'SalesGasMeteringController@destroy')->name('delete');
    });

    Route::prefix('/vessel-operation')->name('vessel-operation.')->group(function () {
        Route::get('/', 'VesselOperationController@index')->name('index');
        Route::post('/store', 'VesselOperationController@store')->name('store');
        Route::get('/{id}', 'VesselOperationController@show')->name('show');
        Route::put('/{id}/update', 'VesselOperationController@update')->name('update');
        Route::delete('/{id}/delete', 'VesselOperationController@destroy')->name('delete');
    });

    // Daily Summary
    Route::prefix('/daily-summary')->name('daily_summary.')->group(function () {
        Route::get('/', 'DailySummaryController@index')->name('index');
        Route::get('/{id}', 'DailySummaryController@show')->name('show');
        Route::get('/vessel/{vesselId}/date/{date}', 'DailySummaryController@byVesselAndDate')->name('by_vessel_date');
        Route::post('/calculate', 'DailySummaryController@calculate')->name('calculate');
        Route::get('/kpi-dashboard', 'DailySummaryController@kpiDashboard')->name('kpi_dashboard');
    });
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardOverviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::namespace('App\Http\Controllers')->name('api.')->group(function(){

    // Base Routes - Public endpoints
    Route::prefix('/base')->name('base.')->group(function () {
        Route::get('/', 'BaseController@index')->name('index');
        Route::get('/set-lang/{lang}','BaseController@setLang');
        Route::get('/barcode', 'BaseController@barcode')->name('barcode');
        Route::get('/menu', 'BaseController@menu')->name('menu');
        Route::get('/timezone', 'BaseController@timezone')->name('timezone');
        Route::get('/csrf-cookie', 'BaseController@csrfCookie')->name('csrf_cookie');
    });
    
    // Authentication routes with rate limiting
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::post('/login', 'AuthController@login')->name("login");
    });

    // Protected routes - require authentication
    Route::group(['middleware' => ['auth:sanctum']], function () {
        
        // Authentication
        Route::post('/logout', 'AuthController@logout')->name("logout");
        
        // Menu Routes
        Route::prefix('/menu')->name('menu.')->group(function () {
            Route::get('/', 'MenuController@index')->name('index');
        });

        // Profile Routes
        Route::prefix('/profile')->name('profile.')->group(function () {
            Route::get('/', 'ProfileController@index')->name('index');
            Route::post('/update','ProfileController@update')->name('update');
            Route::post('/password','ProfileController@password')->name('password');
            Route::get('/device', 'ProfileController@device')->name('device');
            
            // Vessel management untuk user
            Route::get('/vessels', 'ProfileController@getVessels')->name('vessels');
            Route::post('/vessel', 'ProfileController@updateVessel')->name('vessel.update');
            Route::post('/device/disconect','ProfileController@deviceDisconnect')->name('device.disconect');
        });

        // Media Routes
        Route::prefix('/media')->name('media.')->group(function () {
            Route::get('/', 'MediaController@index')->name('index');
            Route::post('/store','MediaController@store')->name('store');
            Route::get('/{id}', 'MediaController@show')->name('show');
            Route::post('/{id}/update','MediaController@update')->name('update');
            Route::delete('/{id}/delete','MediaController@destroy')->name('delete');
        });

        // Dashboard Routes
        Route::prefix('/dashboard')->name('dashboard.')->group(function () {
            Route::get('/', 'DashboardController@index')->name('index');
            Route::get('/kpi', 'DashboardController@kpi')->name('kpi');
            Route::get('/widgets', 'DashboardController@widgets')->name('widgets');
        });

        Route::namespace('Settings')->prefix('/settings')->group(function () {

            Route::prefix('/user')->name('user.')->group(function () {
                Route::get('/', 'UserController@index')->name('index');
                Route::post('/store','UserController@store')->name('store');
                Route::get('/stats', 'UserController@stats')->name('stats');
                Route::delete('/bulk-delete','UserController@bulkDelete')->name('bulk.delete');
                Route::get('/{id}', 'UserController@show')->name('show');
                Route::put('/{id}/update','UserController@update')->name('update');
                Route::delete('/{id}/delete','UserController@destroy')->name('delete');
            });
            
            Route::prefix('/permissions')->name('permissions.')->group(function () {
                Route::get('/', 'PermissionController@index')->name('index');
                Route::get('/list', 'PermissionController@list')->name('list');
                Route::post('/store','PermissionController@store')->name('store');
                Route::get('/{id}', 'PermissionController@show')->name('show');
                Route::put('/{id}/update','PermissionController@update')->name('update');
                Route::delete('/{id}/delete','PermissionController@destroy')->name('delete');
            });

            Route::prefix('/language')->name('language.')->group(function () {
                Route::get('/', 'LanguageController@index')->name('index');
                Route::post('/store','LanguageController@store')->name('store');
                Route::get('/{id}', 'LanguageController@show')->name('show');
                Route::post('/{id}/update','LanguageController@update')->name('update');
                Route::delete('/{id}/delete','LanguageController@destroy')->name('delete');
            });
            
            Route::prefix('/module')->name('module.')->group(function () {
                Route::get('/', 'ModuleController@index')->name('index');
                Route::post('/upload','ModuleController@install')->name('install');
                Route::post('/{name}/toggle', 'ModuleController@toggle')->name('toggle');
                Route::post('/{name}/enable','ModuleController@enable')->name('enable');
                Route::post('/{name}/disable', 'ModuleController@disable')->name('disable');
                Route::delete('/{id}/delete','ModuleController@destroy')->name('delete');
            });

            Route::prefix('/general')->name('general.')->group(function () {
                Route::get('/', 'SystemController@general');
                Route::post('/update','SystemController@generalUpdate');
            });
            
            Route::prefix('/email')->name('email.')->group(function () {
                Route::get('/', 'SystemController@email');
                Route::post('/update','SystemController@emailUpdate');
                Route::post('/test','SystemController@emailTest');
            });
        });
        // Load modular routes
        require __DIR__ . '/Modules/master.php';
        require __DIR__ . '/Modules/production.php';
        
        // Activity Log Routes
        Route::prefix('activity-log')->name('activity-log.')->group(function () {
            Route::get('/', 'ActivityLogController@index')->name('index');
            Route::get('/statistics', 'ActivityLogController@statistics')->name('statistics');
            Route::get('/{id}', 'ActivityLogController@show')->name('show');
            Route::get('/model/{modelType}/{modelId}', 'ActivityLogController@showForModel')->name('model');
            Route::get('/user/{userId}', 'ActivityLogController@showForUser')->name('user');
        });
        
        // Localization Routes
        Route::prefix('localization')->name('localization.')->group(function () {
            Route::get('/current', 'Api\LocalizationController@getCurrentLocale')->name('current');
            Route::get('/supported', 'Api\LocalizationController@getSupportedLocales')->name('supported');
            Route::post('/set', 'Api\LocalizationController@setLocale')->name('set');
            Route::post('/activity-messages', 'Api\LocalizationController@getActivityMessages')->name('activity-messages');
            Route::post('/format-number', 'Api\LocalizationController@formatNumber')->name('format-number');
            Route::post('/format-currency', 'Api\LocalizationController@formatCurrency')->name('format-currency');
            Route::post('/format-date', 'Api\LocalizationController@formatDate')->name('format-date');
            Route::get('/vessel-locale', 'Api\LocalizationController@getVesselLocale')->name('vessel-locale');
            Route::get('/report-locale', 'Api\LocalizationController@getReportLocale')->name('report-locale');
        });
    });
});


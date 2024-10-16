<?php

use App\Http\Controllers;
use App\Http\Controllers\Api\Parts;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

// API routes for user and parts management
Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/', [Controllers\Api\UserManagementController::class, 'getCurrentUser']);
    });
    Route::group(['prefix' => 'parts', 'as' => 'parts.'], function () {
        Route::group(['prefix' => 'team', 'as' => 'team.'], function () {
            Route::get('/{team_id}', [Controllers\Api\PartsManagementController::class, 'getTeamParts']);
            Route::post('/{team_id}/associate', [Controllers\Api\PartsManagementController::class, 'associateTeamParts'])->name('associate');

            Route::post('/upload', [Controllers\Api\PartsManagementController::class, 'updateTeamPartPricing']);
        });

        Route::post('/upload', [Controllers\Api\PartsManagementController::class, 'uploadParts'])->name('upload');
    });
});

Route::get('{any}', function () {
    return view('home');
})->where('any', '.*');

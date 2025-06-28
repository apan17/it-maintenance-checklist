<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistAttachmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceStatusController;
use App\Http\Controllers\MaintenanceAttachmentController;
use App\Http\Controllers\RefComponentController;
use App\Http\Controllers\RefMaintenanceFrequencyController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// PROTECTED ROUTES
Route::group(['middleware' => ['auth:api']], function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::group(["prefix" => "auth"], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('role', [AuthController::class, 'getRole']);
    });

    Route::group(["prefix" => "user"], function () {
        Route::post('{id}/deactivate', [UserController::class, 'deactivateUser']);
    });

});

Route::apiResources([
    'user' => UserController::class,
    
    'asset' => AssetController::class,

    'checklist' => ChecklistController::class,
    'checklist-attachment' => ChecklistAttachmentController::class,

    'maintenance' => MaintenanceController::class,
    // 'maintenance-status' => MaintenanceStatusController::class,
    'maintenance-attachment' => MaintenanceAttachmentController::class,

    'ref-component' => RefComponentController::class,
    'ref-maintenance-frequency' => RefMaintenanceFrequencyController::class,

], ['middleware' => ['auth:api']]);
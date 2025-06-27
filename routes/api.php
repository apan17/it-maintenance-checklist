<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StaffSalaryController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// PROTECTED ROUTES
Route::group(['middleware' => ['auth:api']], function () {

    Route::group(["prefix" => "auth"], function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::group(["prefix" => "user"], function () {
        Route::post('{id}/deactivate', [UserController::class, 'deactivateUser']);
    });

});

Route::apiResources([
    'user' => UserController::class,
    'salary' => SalaryController::class,
    'staff-salary' => StaffSalaryController::class,
], ['middleware' => ['auth:api']]);
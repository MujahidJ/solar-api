<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Technician\ConditionEventController;

use App\Http\Controllers\Admin\InstallationController as AdminInstallationController;
use App\Http\Controllers\Admin\InstallationAssignmentController;
use App\Http\Controllers\Admin\MaintenancePlanController;
use App\Http\Controllers\Admin\ReminderGenerationController;

use App\Http\Controllers\Technician\InstallationController as TechInstallationController;
use App\Http\Controllers\Technician\ServiceVisitController;

use App\Http\Controllers\Client\InstallationController as ClientInstallationController;

use App\Http\Controllers\Client\ReminderController as ClientReminderController;
use App\Http\Controllers\Technician\ReminderController as TechnicianReminderController;

use App\Http\Controllers\Admin\ReminderController as AdminReminderController;

use App\Http\Controllers\NotificationTokenController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', fn (Request $request) => $request->user());

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/notification-tokens', [NotificationTokenController::class, 'store']);
});

/**
 * ADMIN
 */
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/installations', [AdminInstallationController::class, 'index']);
    Route::post('/installations', [AdminInstallationController::class, 'store']);
    Route::get('/installations/{installation}', [AdminInstallationController::class, 'show']);

    Route::post('/reminders/generate-due', [ReminderGenerationController::class, 'generateDue']);
    Route::post('/installations/{installation}/assign-technician', [InstallationAssignmentController::class, 'assign']);
    Route::post('/installations/{installation}/maintenance-plans', [MaintenancePlanController::class, 'store']);
    Route::get('/reminders', [AdminReminderController::class, 'index']);
});

/**
 * TECHNICIAN
 */
Route::middleware(['auth:sanctum', 'role:technician'])->prefix('technician')->group(function () {
    Route::get('/installations', [TechInstallationController::class, 'index']);
    Route::get('/reminders', [TechnicianReminderController::class, 'index']);
    Route::post('/installations/{installation}/service-visits', [ServiceVisitController::class, 'store']);
    Route::post('/installations/{installation}/condition-events', [ConditionEventController::class, 'store']);

});

/**
 * CLIENT
 */
Route::middleware(['auth:sanctum', 'role:client'])->prefix('client')->group(function () {
    Route::get('/installations', [ClientInstallationController::class, 'index']);
    Route::get('/installations/{installation}', [ClientInstallationController::class, 'show']);
    Route::get('/reminders', [ClientReminderController::class, 'index']);
});
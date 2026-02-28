<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/ping', fn () => ['ok' => true, 'role' => 'admin']);
});

Route::middleware(['auth:sanctum', 'role:technician'])->group(function () {
    Route::get('/technician/ping', fn () => ['ok' => true, 'role' => 'technician']);
});

Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
    Route::get('/client/ping', fn () => ['ok' => true, 'role' => 'client']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DistribusiController;
use App\Http\Controllers\Api\ReturController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/distribusi', [DistribusiController::class, 'index']);
    Route::post('/distribusi/{distribusi}/confirm-delivered', [DistribusiController::class, 'confirmDelivered']);
    Route::get('/retur', [ReturController::class, 'index']);
    Route::post('/retur/{retur}/confirm-pickup', [ReturController::class, 'confirmPickup']);

    //Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

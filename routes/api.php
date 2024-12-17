<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentDataController;
use App\Http\Controllers\FieldTypeController;


Route::middleware('throttle:api')->group(function () {
    // API routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/{id}', [DocumentController::class, 'show']);
        Route::put('/{id}', [DocumentController::class, 'update']);
        Route::delete('/{id}', [DocumentController::class, 'destroy']);
        Route::post('/triggerLogicApp', [DocumentController::class, 'triggerLogicApp']);
    });

    Route::prefix('document-data')->group(function () {
        Route::get('/', [DocumentDataController::class, 'index']);
        Route::get('/{documentId}', [DocumentDataController::class, 'show']);
    });

    Route::prefix('fields')->group(function () {
        Route::get('/', [FieldTypeController::class, 'index']);
        Route::get('/{id}', [FieldTypeController::class, 'show']);
    });
});

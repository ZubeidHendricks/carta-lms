<?php

use Illuminate\Support\Facades\Route;
use Modules\Tavus\Http\Controllers\TavusReplicaController;
use Modules\Tavus\Http\Controllers\TavusVideoController;
use Modules\Tavus\Http\Controllers\TavusConversationController;
use Modules\Tavus\Http\Controllers\TavusWebhookController;
use Modules\Tavus\Http\Controllers\TavusPersonaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/tavus')->middleware(['auth:sanctum'])->group(function () {
    
    // Replicas
    Route::prefix('replicas')->group(function () {
        Route::get('/', [TavusReplicaController::class, 'index']);
        Route::post('/', [TavusReplicaController::class, 'store']);
        Route::get('/{replica}', [TavusReplicaController::class, 'show']);
        Route::post('/{replica}/sync', [TavusReplicaController::class, 'sync']);
        Route::delete('/{replica}', [TavusReplicaController::class, 'destroy']);
    });

    // Videos
    Route::prefix('videos')->group(function () {
        Route::get('/', [TavusVideoController::class, 'index']);
        Route::post('/', [TavusVideoController::class, 'store']);
        Route::get('/{video}', [TavusVideoController::class, 'show']);
        Route::post('/{video}/sync', [TavusVideoController::class, 'sync']);
        Route::delete('/{video}', [TavusVideoController::class, 'destroy']);
    });

    // Conversations
    Route::prefix('conversations')->group(function () {
        Route::get('/', [TavusConversationController::class, 'index']);
        Route::post('/', [TavusConversationController::class, 'store']);
        Route::get('/{conversation}', [TavusConversationController::class, 'show']);
        Route::post('/{conversation}/end', [TavusConversationController::class, 'end']);
    });

    // Personas
    Route::prefix('personas')->group(function () {
        Route::get('/', [TavusPersonaController::class, 'index']);
        Route::post('/', [TavusPersonaController::class, 'store']);
        Route::get('/presets', [TavusPersonaController::class, 'presets']);
        Route::post('/presets', [TavusPersonaController::class, 'createPreset']);
        Route::get('/{persona}', [TavusPersonaController::class, 'show']);
        Route::put('/{persona}', [TavusPersonaController::class, 'update']);
        Route::delete('/{persona}', [TavusPersonaController::class, 'destroy']);
        Route::post('/{persona}/toggle', [TavusPersonaController::class, 'toggle']);
        Route::get('/{persona}/configuration', [TavusPersonaController::class, 'configuration']);
    });
});

// Webhook (no auth required)
Route::post('api/tavus/webhook', [TavusWebhookController::class, 'handle']);

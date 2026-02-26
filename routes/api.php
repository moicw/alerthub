<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AlertRuleController;
use App\Http\Controllers\WebhookSourceController;
use App\Http\Controllers\WebhookController;

Route::middleware('tenant')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

    Route::get('/projects/{project}/subscribers', [SubscriberController::class, 'index']);
    Route::post('/projects/{project}/subscribers', [SubscriberController::class, 'store']);

    Route::get('/projects/{project}/notifications', [NotificationController::class, 'index']);

    Route::post('/projects/{project}/alert-rules', [AlertRuleController::class, 'store']);
    Route::get('/projects/{project}/alert-rules', [AlertRuleController::class, 'index']);

    Route::post('/projects/{project}/webhook-sources', [WebhookSourceController::class, 'store']);
    Route::get('/projects/{project}/webhook-sources', [WebhookSourceController::class, 'index']);
});

Route::post('/webhooks/{project_uuid}/{source_key}', [WebhookController::class, 'handle']);

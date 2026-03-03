<?php

use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\EnrichmentMockController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/{source}', [WebhookController::class, 'store']);
Route::post('/fake-enrichment', [EnrichmentMockController::class, 'enrich']);

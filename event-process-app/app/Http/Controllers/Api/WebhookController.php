<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Jobs\NormalizeEventJob;
use App\Services\Sources\SourceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhook requests.
     *
     * @param Request $request
     * @param string $source
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $source)
    {
        // Resolve normalizer
        $normalizer = SourceFactory::make($source);

        if (! $normalizer) {
            return response()->json([
                'error' => 'Unknown source'
            ], Response::HTTP_BAD_REQUEST);
        }

        $payload = $request->all();

        // Extract external ID for idempotency
        try {
            $externalId = $normalizer->extractExternalId($payload);
        } catch (\Throwable $e) {
            Log::error("Failed to extract external ID: " . $e->getMessage(), ['payload' => $payload]);
            return response()->json([
                'error' => 'Invalid payload structure'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Prevent duplicates (idempotency)
        $event = Event::where('external_id', $externalId)
            ->where('source', $source)
            ->first();

        if ($event) {
            // Idempotent behavior — return OK but do nothing
            return response()->json(['ok' => true]);
        }

        // Store raw event immediately
        $event = Event::create([
            'uuid' => (string) Str::uuid(),
            'external_id' => $externalId,
            'type' => $source, // For simplicity, using source as type. In real scenarios, this might be more specific.
            'source' => $source,
            'score' => 0,
            'raw_payload' => $payload,
            'status' => 'received',
            'created_at' => now(),
        ]);

        // Dispatch async normalization job
        NormalizeEventJob::dispatch($event->id);

        // Immediate 200 response
        return response()->json(['ok' => true]);
    }
}
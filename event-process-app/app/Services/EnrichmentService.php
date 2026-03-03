<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * EnrichmentService is responsible for enriching an event by calling an external enrichment API.
 * It handles the logic for making the HTTP request, processing the response, and implementing retry logic with exponential backoff.
 */
class EnrichmentService
{
    public function enrich(Event $event): array
    {
        $url = (string) config('services.enrichment.url');
        $token = (string) config('services.enrichment.token', '');
        $timeout = (int) config('services.enrichment.timeout', 5);

        $maxAttempts = 3;
        $backoffMs = [300, 900, 1800];

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::acceptJson()
                    ->when($token !== '', fn ($http) => $http->withToken($token))
                    ->timeout($timeout)
                    ->post($url, [
                        'event_id' => $event->id,
                        'type' => $event->source
                    ]);

                if ($response->successful()) {
                    return ['ok' => true, 'status' => 'enriched', 'data' => $response->json(), 'error' => null, 'attempts' => $attempt];
                }

                if (in_array($response->status(), [429, 500, 502, 503, 504], true) && $attempt < $maxAttempts) {
                    usleep($backoffMs[$attempt - 1] * 1000);
                    continue;
                }

                return ['ok' => false, 'status' => 'fallback', 'data' => null, 'error' => 'HTTP_'.$response->status(), 'attempts' => $attempt];
            } catch (ConnectionException) {
                if ($attempt < $maxAttempts) {
                    usleep($backoffMs[$attempt - 1] * 1000);
                    continue;
                }
                return ['ok' => false, 'status' => 'fallback', 'data' => null, 'error' => 'timeout_or_connection_error', 'attempts' => $attempt];
            }
        }

        return ['ok' => false, 'status' => 'fallback', 'data' => null, 'error' => 'unknown_error', 'attempts' => $maxAttempts];
    }
}

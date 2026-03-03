<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Services\Sources\SourceFactory;
use App\Models\EventScoreRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\EnrichmentService;
use \App\Models\Event;
use InvalidArgumentException;
use Throwable;


class NormalizeEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 15, 30];

    public function __construct(public int $eventId)
    {
    }

    /**
     * Execute the job.
     * This method retrieves the raw event from the database, processes it using the appropriate normalizer based on the source, and updates the event record with the normalized payload or any errors encountered during processing.
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        Log::info('NormalizeEventJob started', ['event_id' => $this->eventId]);

        $event = Event::where('id', $this->eventId)->first();

        if ($event === null) {
            Log::warning("Event not found", ['event_id' => $this->eventId]);
            return;
        }

        DB::table('events')->where('id', $this->eventId)->update([
            'status' => 'processing',
            'updated_at' => now(),
        ]);

        try {
            $normalizer = SourceFactory::make((string) $event->source);
            $payload = $this->decodePayload($event->raw_payload);

            $validated = $normalizer->validate($payload);
            $normalized = $normalizer->normalize($validated);
            $scoreRules =  new EventScoreRule();
            $score = $scoreRules->calculateScore($normalized['normalized_data'] ?? []);
            // call to enrichment service to get additional data based on the normalized event, this is just a mock and can be replaced with actual enrichment logic
            $enrichmentService = new EnrichmentService();
            $enrichmentData = $enrichmentService->enrich($event);

            // TODO: here enrichment with external data sources could be implemented before saving the normalized event

            DB::table('events')->where('id', $this->eventId)->update([
                'external_id' => $normalized['external_id'] ?? null,
                'status' => 'processed',
                'occurred_at' => $normalized['occurred_at'] ?? null,
                'type' => $normalized['type'] ?? null,
                'score' => $score,
                'enrichment' => $enrichmentData,
                'normalized_payload' => $normalized['normalized_data'] ?? null,
                'error' => null,
                'updated_at' => now(),
            ]);
        } catch (ValidationException $exception) {
            DB::table('events')->where('id', $this->eventId)->update([
                'status' => 'failed',
                'error' => json_encode([
                    'message' => 'Normalization validation failed',
                    'validation_errors' => $exception->errors(),
                ], JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        } catch (InvalidArgumentException $exception) {
            DB::table('events')->where('id', $this->eventId)->update([
                'status' => 'failed',
                'error' => json_encode([
                    'message' => 'Unsupported source during normalization',
                    'details' => $exception->getMessage(),
                ], JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
            Log::error('NormalizeEventJob failed', ['event_id' => $this->eventId, 'error' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            DB::table('events')->where('id', $this->eventId)->update([
                'status' => 'failed',
                'error' => json_encode([
                    'message' => 'Normalization job failed',
                    'details' => $exception->getMessage(),
                ], JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
            Log::error('NormalizeEventJob failed', ['event_id' => $this->eventId, 'error' => $exception->getMessage()]);

            throw $exception;
        }
    }

    private function decodePayload(mixed $rawPayload): array
    {
        if (is_array($rawPayload)) {
            return $rawPayload;
        }

        if (is_string($rawPayload) && $rawPayload !== '') {
            $decoded = json_decode($rawPayload, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}

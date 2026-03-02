<?php

namespace App\Services\Sources\Normalizers;

use App\Services\Sources\Contracts\SourceNormalizer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

/**
 * Class StatusTrackerNormalizer
 *
 * This class is responsible for validating and normalizing incoming event payloads from a status tracking source.
 * It implements the SourceNormalizer interface, ensuring that it provides the necessary methods for validation and normalization.
 * a status tracker is a service that tracks the status of an order, shipment, or any other process. The payload typically includes information about the event, such as the event ID, status, tracking number, and metadata about the event. Status tracking providers like AfterShip, ShipStation, EasyPost, etc.
 * Example payload:
 * {
 *     "tracking_number": "TRACK123456",
 *     "status": "shipped",
 *     "occurred_at": "2024-06-01T12:00:00Z",
 *     "email": "user@example.com"
 * }
 */
class StatusTrackerNormalizer implements SourceNormalizer
{
    /**
     * Validate the incoming payload from the status tracker.
     *
     * @param array $payload The raw payload received from the status tracker.
     * @return array The validated and sanitized payload.
     * @throws ValidationException If the payload fails validation.
     */
    public function validate(array $payload): array
    {
        $validator = Validator::make($payload, [
            'tracking_number' => ['required', 'string'],
            'status' => ['required', 'string'],
            'occurred_at' => ['nullable', 'date'],
            'email' => ['nullable', 'email'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Normalize the validated payload into a consistent format for storage and further processing.
     *
     * @param array $payload The validated payload from the status tracker.
     * @return array The normalized payload with standardized keys and formats.
     */    
    public function normalize(array $payload): array
    {
        
        $normalizedData = ['tracking_number' => $payload['tracking_number'], 'status' => $payload['status'], 'type' => 'status_update'];
        $date = isset($payload['occurred_at']) ? Carbon::parse($payload['occurred_at']) : Carbon::now();
        $normalizedData['occurred_at'] = $date->toISOString();

        return [
            'external_id' => $payload['tracking_number'],
            'email' => $payload['email'] ?? null,
            'status' => $payload['status'],
            'occurred_at' => $date->toDateTimeString(),
            'type' => 'status_update',
            'normalized_data' => $normalizedData,
        ];
    }

    /**
     * Extract the external ID from the payload.
     *
     * @param array $payload The raw payload received from the status tracker.
     * @return string The external ID of the event.
     * @throws \InvalidArgumentException If the external ID is missing or invalid.
     */
    public function extractExternalId(array $payload): string
    {
        if (!isset($payload['tracking_number']) || !is_string($payload['tracking_number'])) {
            throw new \InvalidArgumentException("Missing or invalid tracking_number in payload");
        }

        return $payload['tracking_number'];
    }
}

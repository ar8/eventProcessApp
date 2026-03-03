<?php

namespace App\Services\Sources\Normalizers;

use App\Services\Sources\Contracts\SourceNormalizer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

/**
 * Class FormProviderNormalizer
 *
 * This class is responsible for validating and normalizing incoming event payloads from a form provider source.
 * It implements the SourceNormalizer interface, ensuring that it provides the necessary methods for validation and normalization.
 * a form provider is a service that collects user input through forms, such as contact forms, surveys, or lead generation forms. The payload typically includes information about the form submission, such as the submitter's email, answers to form questions, and metadata about the submission. example fof form provider are: Typeform, Google Forms, JotForm, Wufoo, Formstack, etc.
 * example payload:
 * {
 *     "submission_id": "abc123",
 *     "email": "user@example.com",
 *     "answers": {
 *         "budget": 1000,
 *         "timeline": "Q3 2024",
 *         "interested_in": ["product_a", "product_b"],
 *     },
 *     "submitted_at": "2024-06-01T12:00:00Z"
 *     "metadata": {
 *         "source": "typeform",
 *         "form_id": "form_456"
 *     }    
 * }
 */
class FormProviderNormalizer implements SourceNormalizer
{
    /**
     * Validate the incoming payload from the form provider.
     *
     * @param array $payload The raw payload received from the form provider.
     * @return array The validated and sanitized payload.
     * @throws ValidationException If the payload fails validation.
     */
    public function validate(array $payload): array
    {
        $validator = Validator::make($payload, [
            'submission_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'answers' => ['required', 'array'],
            'submitted_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Normalize the validated payload into a consistent format for storage and further processing.
     *
     * @param array $payload The validated payload from the form provider.
     * @return array The normalized payload with standardized keys and formats.
     */
    public function normalize(array $payload): array
    {
        // TODO add scoring logic based on the answers provided in the form submission. For example, if the budget is above a certain threshold, or if the timeline is within a specific range, we can assign a higher score to indicate a more promising lead. For now, we'll set a default score of 0.

        $date = isset($payload['submitted_at']) ? Carbon::parse($payload['submitted_at']) : Carbon::now();
        $normializedData = ['answers' => $payload['answers'], 'submitted_at' => $date->toISOString(), 'status' => 'new', 'type' => 'form_provider', 'form_status' =>'new'];

        return [
            'external_id' => $payload['submission_id'],
            'occurred_at' => $date->toDateTimeString(),
            'type' => 'form_provider',
            'normalized_data' => $normializedData, // Store the answers in a normalized_data field for further processing and analysis
            
        ];
    }

    /**
     * Extract the external ID from the payload.
     *
     * @param array $payload The raw payload received from the form provider.
     * @return string The external ID of the form submission.
     * @throws \InvalidArgumentException If the external ID is missing or invalid.
     */
    public function extractExternalId(array $payload): string
    {
        if (!isset($payload['submission_id']) || !is_string($payload['submission_id'])) {
            throw new \InvalidArgumentException('Missing or invalid submission_id in payload');
        }

        return $payload['submission_id'];
    }
}

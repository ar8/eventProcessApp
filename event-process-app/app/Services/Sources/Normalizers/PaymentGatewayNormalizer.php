<?php

namespace App\Services\Sources\Normalizers;

use App\Services\Sources\Contracts\SourceNormalizer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;


/**
 * Class PaymentGatewayNormalizer
 *
 * This class is responsible for validating and normalizing incoming event payloads from a payment gateway source.
 * It implements the SourceNormalizer interface, ensuring that it provides the necessary methods for validation and normalization.
 * a payment gateway is a service that processes online payments, such as Stripe, PayPal, Square.
 * The payload typically includes information about the transaction, such as the transaction ID, amount, currency, customer email, and metadata about the transaction. example payload:
 * {
 *     "transaction": {
 *         "id": "txn_123456",
 *         "amount_cents": 5000,
 *         "currency": "USD",
 *         "occurred_at": "2024-06-01T12:00:00Z",
 *         "status": "succeeded",
 *         "provider": "paypal"
 *     },
 *     "customer_email": "user@example.com"
 * }
 */
class PaymentGatewayNormalizer implements SourceNormalizer
{
    /**
     * Validate the incoming payload from the payment gateway.
     *
     * @param array $payload The raw payload received from the payment gateway.
     * @return array The validated and sanitized payload.
     * @throws ValidationException If the payload fails validation.
     */
    public function validate(array $payload): array
    {
        $validator = Validator::make($payload, [
            'transaction' => ['required', 'array'],
            'transaction.id' => ['required', 'string'],
            'transaction.amount_cents' => ['required', 'integer', 'min:0'],
            'transaction.currency' => ['required', 'string', 'size:3'],
            'customer_email' => ['required', 'email'],
            'transaction.occurred_at' => ['nullable', 'date'],
            'transaction.status' => ['required', 'string', 'in:succeeded,failed,pending'],
            'transaction.provider' => ['required', 'string', 'in:stripe,paypal,square'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Normalize the validated payload into a consistent format for storage and further processing.
     *
     * @param array $payload The validated payload from the payment gateway.
     * @return array The normalized payload with standardized keys and formats.
     */
    public function normalize(array $payload): array    
    {
        $normalizedData = ['provider' => $payload['transaction']['provider']];
        $normalizedData['amount'] = $payload['transaction']['amount_cents'] / 100; // Convert cents to dollars
        $normalizedData['currency'] = $payload['transaction']['currency'];
        $normalizedData['status'] = $payload['transaction']['status'];
        $date = isset($payload['transaction']['occurred_at']) ? Carbon::parse($payload['transaction']['occurred_at']) : Carbon::now();
        $normalizedData['occurred_at'] = $date->toISOString();

        return [
            'external_id' => $payload['transaction']['id'],
            'email' => $payload['customer_email'],
            'status' => $payload['transaction']['status'],
            'occurred_at' => $date->toDateTimeString(),
            'type' => 'payment_transaction',
            'normalized_data' => $normalizedData, // Store the transaction details in a normalized_data field for further processing and analysis
        ];
    }

    /**
     * Extract the external ID from the payload.
     *
     * @param array $payload The raw payload received from the payment gateway.
     * @return string The external ID of the transaction.
     * @throws \InvalidArgumentException If the external ID is missing or invalid.
     */
    public function extractExternalId(array $payload): string
    {
        if (!isset($payload['transaction']['id']) || !is_string($payload['transaction']['id'])) {
            throw new \InvalidArgumentException('Missing or invalid transaction ID in payload');
        }

        return $payload['transaction']['id'];
    }
}

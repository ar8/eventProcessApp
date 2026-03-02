<?php

namespace App\Services\Sources\Contracts;

/**
 * Interface SourceNormalizer
 *
 * Defines the contract for normalizing incoming event payloads from various sources.
 * Each source (e.g., GitHub, PayPal) will have its own implementation of this interface.
 */
interface SourceNormalizer
{
    public function extractExternalId(array $payload): string;

    public function validate(array $payload): array;

    public function normalize(array $payload): array;
}

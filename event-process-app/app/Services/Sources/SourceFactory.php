<?php

namespace App\Services\Sources;

use App\Services\Sources\Contracts\SourceNormalizer;
use App\Services\Sources\Normalizers\FormProviderNormalizer;
use App\Services\Sources\Normalizers\PaymentGatewayNormalizer;
use App\Services\Sources\Normalizers\StatusTrackerNormalizer;
use InvalidArgumentException;

class SourceFactory
{
    /**
     * Factory method to create a SourceNormalizer instance based on the source name.
     *
     * @param string $source The name of the source (e.g., 'form_provider', 'payment_gateway', 'status_tracker').
     * @return SourceNormalizer
     * @throws InvalidArgumentException if the source is not supported.
     */
    public static function make(string $source): SourceNormalizer
    {
        return match ($source) {
            'form_provider' => new FormProviderNormalizer(),
            'payment_gateway' => new PaymentGatewayNormalizer(),
            'status_tracker' => new StatusTrackerNormalizer(),
            default => throw new InvalidArgumentException('Unsupported webhook source.'),
        };
    }

    /**
     * Get a list of supported sources.
     *
     * @return array
     */
    public static function supportedSources(): array
    {
        return ['form_provider', 'payment_gateway', 'status_tracker'];
    }
}

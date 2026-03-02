<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use factories to create dummy events
        // pending payment transaction event
        Event::create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'external_id' => 'abc123',
            'source' => 'payment_gateway',
            'raw_payload' => ['transaction' => ['id' => 'abc123', 'amount_cents' => 5000, 'currency' => 'USD', 'status' => 'succeeded', 'provider' => 'Stripe', 'occurred_at' => now()->toISOString()], 'customer_email' => 'user@example.com'],
            'status' => 'pending',
            'type' => 'payment_transaction',
            'score' => 0,
            'normalized_payload' => ['external_id' => 'abc123', 'email' => 'user@example.com', 'occurred_at' => now()->toDateTimeString(), 'type' => 'payment_transaction', 'score' => 0, 'normalized_data' => ['transaction' => ['id' => 'abc123', 'amount_cents' => 5000, 'currency' => 'USD', 'status' => 'succeeded', 'provider' => 'Stripe', 'occurred_at' => now()->toISOString()]]]
        ]);

        // failed payment transaction event
        Event::create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174002',
            'external_id' => 'ghi789',
            'source' => 'payment_gateway',
            'raw_payload' => ['transaction' => ['id' => 'ghi789', 'amount_cents' => 2000, 'currency' => 'USD', 'status' => 'failed', 'provider' => 'PayPal', 'occurred_at' => now()->toISOString()], 'customer_email' => '', 'normalized_data' => ['transaction' => ['id' => 'ghi789', 'amount_cents' => 2000, 'currency' => 'USD', 'status' => 'failed', 'provider' => 'PayPal', 'occurred_at' => now()->toISOString()]]],
            'status' => 'pending',
            'type' => 'payment_transaction',
            'score' => 0,
            'normalized_payload' => ['external_id' => 'ghi789', 'email' => '', 'occurred_at' => now()->toDateTimeString(), 'type' => 'payment_transaction', 'score' => 0, 'normalized_data' => ['transaction' => ['id' => 'ghi789', 'amount_cents' => 2000, 'currency' => 'USD', 'status' => 'failed', 'provider' => 'PayPal']]]
        ]); 

        // form submission event product interested
        Event::create([
                'uuid' => '123e4567-e89b-12d3-a456-426614174001',
                'external_id' => 'def456',
                'source' => 'form_provider',
                'raw_payload' => ['submission_id' => 'def456', 'email' => 'user@example.com', 'answers' => ['budget' => 1000, 'timeline' => 'Q3 2024', 'interested_in' => ['product_a', 'product_b']], 'submitted_at' => now()->toISOString(), 'metadata' => ['source' => 'typeform', 'form_id' => 'form_456']],
                'status' => 'pending',
                'type' => 'form_submission',
                'score' => 0,
                'normalized_payload' => ['external_id' => 'def456', 'email' => 'user@example.com', 'occurred_at' => now()->toDateTimeString(), 'type' => 'form_submission', 'score' => 0, 'normalized_data' => ['answers' => ['budget' => 1000, 'timeline' => 'Q3 2024', 'interested_in' => ['product_a', 'product_b']], 'submitted_at' => now()->toISOString()]]
        ]);

        // complaint form submission event
        Event::create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174003',
            'external_id' => 'jkl012',
            'source' => 'form_provider',
            'raw_payload' => ['submission_id' => 'jkl012', 'email' => 'user@example.com', 'answers' => ['complaint' => 'Product not working as expected'], 'submitted_at' => now()->toISOString(), 'metadata' => ['source' => 'typeform', 'form_id' => 'form_789']],
            'status' => 'pending',
            'type' => 'form_submission',
            'score' => 0,
            'normalized_payload' => ['external_id' => 'jkl012', 'email' => 'user@example.com', 'occurred_at' => now()->toDateTimeString(), 'type' => 'form_submission', 'score' => 0, 'normalized_data' => ['answers' => ['complaint' => 'Product not working as expected'], 'submitted_at' => now()->toISOString()]]
        ]);

        // customer support form submission event
        Event::create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174004',
            'external_id' => 'mno345',
            'source' => 'form_provider',
            'raw_payload' => ['submission_id' => 'mno345', 'email' => 'user@example.com', 'answers' => ['support_request' => 'Need help with product'], 'submitted_at' => now()->toISOString(), 'metadata' => ['source' => 'typeform', 'form_id' => 'form_101']],
            'status' => 'pending',
            'type' => 'form_submission',
            'score' => 0,
            'normalized_payload' => ['external_id' => 'mno345', 'email' => 'user@example.com', 'occurred_at' => now()->toDateTimeString(), 'type' => 'form_submission', 'score' => 0, 'normalized_data' => ['answers' => ['support_request' => 'Need help with product'], 'submitted_at' => now()->toISOString()]]
        ]);

        // shipped order event
        Event::create([
            'uuid' => '123e4567-e89b-12d3-a456-426614174005',
            'external_id' => 'pqr678',
            'source' => 'status_tracker',
            'raw_payload' => ['tracking_number' => 'pqr678', 'status' => 'shipped', 'occurred_at' => now()->toISOString(), 'email' => 'user@example.com'],
            'status' => 'pending',
            'type' => 'order_status',
            'score' => 0,
            'normalized_payload' => ['external_id' => 'pqr678', 'email' => 'user@example.com', 'occurred_at' => now()->toDateTimeString(), 'type' => 'order_status', 'score' => 0, 'normalized_data' => ['tracking_number' => 'pqr678', 'status' => 'shipped', 'occurred_at' => now()->toISOString()]]
        ]);
    }
}

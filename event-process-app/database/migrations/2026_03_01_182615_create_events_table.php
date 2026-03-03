<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'events' table if it doesn't exist
        Schema::create('events', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->uuid('uuid')->unique();
            $table->string('external_id')->index();
            $table->string('type')->index(); // e.g: 'payment_transaction', 'shipment_update', 'status_update' etc.
            $table->string('source')->index(); // e.g: 'paypal', 'shipstation' etc.
            $table->integer('score')->index();
            $table->string('status')->index()->default('pending'); // 'pending','processing', 'processed', 'failed', 'succeeded', 'enriched', 'enrichment_failed', 'completed' etc.
            $table->json('raw_payload');
            $table->json('normalized_payload')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->json('enrichment')->nullable();
            $table->text('error')->nullable();
            $table->timestamps(); // created_at, updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

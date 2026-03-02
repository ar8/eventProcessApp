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
        Schema::create('event_audits', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('action')->index(); // 'created', 'updated', 'deleted'
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->json('meta')->nullable(); // Store changes for updates or relevant info for deletes e.g: { "status": ["pending", "processed"], "score": [0, 91] }, { "status": "failed", "score": 12, "error": "invalid schema" }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_audits');
    }
};

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
        Schema::create('event_scores_rules', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->string('field');
            $table->string('operator'); // contains, equals etc.
            $table->string('value');
            $table->integer('points')->default(2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_score_rules');
    }
};

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
        Schema::dropIfExists('correlation_analyses');

        Schema::create('correlation_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hacker_attack_id')->constrained('hacker_attacks')->onDelete('cascade');
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->float('correlation_score'); // 0-100, grau de relação
            $table->text('analysis_reason');    // Por que foram correlacionadas
            $table->string('correlation_type'); // direct, temporal, entity_based, etc
            $table->date('analysis_date');
            $table->boolean('is_validated')->default(false);
            $table->json('pattern_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correlation_analyses');
    }
};

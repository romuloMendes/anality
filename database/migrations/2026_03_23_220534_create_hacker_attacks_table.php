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
        Schema::create('hacker_attacks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('attack_type'); // DDoS, Ransomware, Phishing, etc
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('affected_entity')->nullable();
            $table->date('attack_date');
            $table->string('source_url')->nullable();
            $table->string('source_name'); // CVE Database, Dark Web, News sites, etc
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hacker_attacks');
    }
};

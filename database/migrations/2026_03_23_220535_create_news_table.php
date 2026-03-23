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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('source_name'); // G1, CNN, Reuters, etc
            $table->string('source_url')->nullable();
            $table->date('published_date');
            $table->string('category'); // Segurança, Tecnologia, Política, etc
            $table->json('keywords')->nullable();
            $table->text('summary')->nullable();
            $table->integer('relevance_score')->default(0); // 1-10
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

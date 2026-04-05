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
        Schema::create('attack_import_batches', function (Blueprint $table): void {
            $table->id();
            $table->string('filename');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('imported_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->enum('status', ['completed', 'reverted'])->default('completed');
            $table->timestamp('reverted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attack_import_batches');
    }
};

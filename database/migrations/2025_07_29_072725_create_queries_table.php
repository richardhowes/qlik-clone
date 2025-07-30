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
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_source_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('sql');
            $table->json('result_metadata')->nullable();
            $table->integer('execution_time')->nullable(); // milliseconds
            $table->integer('row_count')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'data_source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};

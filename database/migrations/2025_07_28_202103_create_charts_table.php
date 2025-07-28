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
        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // bar, line, pie, table, kpi
            $table->text('query')->nullable();
            $table->json('config'); // chart configuration
            $table->json('position')->nullable(); // x, y coordinates
            $table->json('size')->nullable(); // width, height
            $table->foreignId('dashboard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_source_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charts');
    }
};

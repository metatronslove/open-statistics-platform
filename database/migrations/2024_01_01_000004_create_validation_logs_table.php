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
        Schema::create('validation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('calculated_average', 15, 4);
            $table->decimal('standard_deviation', 15, 4);
            $table->enum('status', ['pending', 'verified', 'failed'])->default('pending');
            $table->json('outliers')->nullable();
            $table->integer('total_points');
            $table->integer('valid_points');
            $table->timestamps();
            
            $table->unique(['dataset_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_logs');
    }
};

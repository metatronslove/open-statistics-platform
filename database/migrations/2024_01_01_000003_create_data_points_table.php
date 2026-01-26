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
        Schema::create('data_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_provider_id')->constrained('data_providers')->onDelete('cascade');
            $table->date('date');
            $table->decimal('value', 15, 4);
            $table->string('source_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->decimal('verified_value', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['dataset_id', 'data_provider_id', 'date']);
            $table->index(['dataset_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_points');
    }
};

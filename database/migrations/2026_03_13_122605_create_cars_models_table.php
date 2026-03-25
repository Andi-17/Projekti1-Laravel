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
        Schema::create('cars_models', function (Blueprint $table) {
            $table->id();
        

            $table->foreignId('brand_id')
            ->constrained('cars_brands')
            ->cascadeOnDelete();


            $table->string('name');
            $table->string('slug');

            $table->year('year_start')->nullable();
            $table->year('year_end')->nullable();
            
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars_models');
    }
};

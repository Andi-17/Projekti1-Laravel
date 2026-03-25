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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('brand_id')
                ->constrained('cars_brands')
                ->cascadeOnDelete();
        
            $table->foreignId('model_id')
                ->constrained('cars_models')
                ->cascadeOnDelete();
        
            $table->string('title');
            $table->text('description')->nullable();
        
            $table->year('year');
            $table->string('color')->nullable();
        
            $table->enum('fuel_type', ['petrol','diesel','hybrid','electric']);
            $table->enum('transmission', ['manual','automatic']);
        
            $table->string('engine')->nullable();
            $table->integer('horsepower')->nullable();
            $table->integer('mileage')->nullable();
        
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('EUR');
        
            $table->enum('status', ['available','sold','reserved'])->default('available');
        
            $table->boolean('featured')->default(false);
            $table->string('main_image')->nullable();
        
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};

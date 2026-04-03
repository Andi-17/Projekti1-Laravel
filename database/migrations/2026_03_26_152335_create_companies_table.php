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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
        
            $table->string('name');
        
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
        
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
        
            $table->string('business_number')->nullable();
            $table->string('vat_number')->nullable();
        
            $table->string('logo')->nullable();
        
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
        
            $table->boolean('is_active')->default(true);
        
            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

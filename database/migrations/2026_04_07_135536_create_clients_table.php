<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // 🔗 lidhja me company
            $table->foreignId('company_id')
                  ->constrained()
                  ->cascadeOnDelete();

           
            $table->enum('client_type', ['business', 'private']);

           
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

          
            $table->string('business_name')->nullable();
            $table->string('additional_company_name')->nullable();

           
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->string('additional_address_info')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

    
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();

         
            $table->string('client_language')->nullable();
            $table->text('remarks')->nullable();
            $table->string('category')->nullable();
            $table->integer('employees_count')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

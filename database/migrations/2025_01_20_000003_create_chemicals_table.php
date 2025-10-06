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
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('chemical_name', 100);
            $table->string('chemical_type', 50)->nullable();
            $table->string('supplier', 100)->nullable();
            $table->string('msds_number', 50)->nullable();
            $table->decimal('unit_cost', 10, 4)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('unit_of_measure', 20)->default('Liter');
            $table->decimal('density_kg_per_liter', 6, 3)->nullable();
            $table->text('safety_notes')->nullable();
            $table->boolean('is_hazardous')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('vessel_id', 'idx_vessel_id');
            $table->index('chemical_name', 'idx_chemical_name');
            $table->index('chemical_type', 'idx_chemical_type');
            $table->index('is_hazardous', 'idx_hazardous');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
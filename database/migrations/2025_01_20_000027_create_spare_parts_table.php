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
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('part_number', 100);
            $table->string('part_name', 200);
            $table->text('part_description')->nullable();
            $table->enum('part_category', ['Engine', 'Electrical', 'Hydraulic', 'Mechanical', 'Safety', 'Instrumentation', 'Other']);
            $table->string('manufacturer', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('specifications', 500)->nullable();
            $table->decimal('current_stock', 10, 2)->default(0.00);
            $table->decimal('minimum_stock', 8, 2)->default(0.00);
            $table->decimal('maximum_stock', 10, 2)->nullable();
            $table->string('unit_of_measure', 20)->default('Pieces');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->string('supplier_name', 200)->nullable();
            $table->string('supplier_part_number', 100)->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->string('storage_location', 200)->nullable();
            $table->enum('criticality', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->date('last_received_date')->nullable();
            $table->date('last_issued_date')->nullable();
            $table->text('compatible_equipment')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'part_number'], 'unique_vessel_part');

            // Indexes
            $table->index('part_number', 'idx_part_number');
            $table->index('part_name', 'idx_part_name');
            $table->index('part_category', 'idx_part_category');
            $table->index('manufacturer', 'idx_manufacturer');
            $table->index('current_stock', 'idx_current_stock');
            $table->index('minimum_stock', 'idx_minimum_stock');
            $table->index('criticality', 'idx_criticality');
            $table->index('supplier_name', 'idx_supplier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
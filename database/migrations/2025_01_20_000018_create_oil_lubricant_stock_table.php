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
        Schema::create('oil_lubricant_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('product_name', 200);
            $table->enum('product_type', ['Engine Oil', 'Hydraulic Oil', 'Gear Oil', 'Grease', 'Coolant', 'Other']);
            $table->string('brand', 100)->nullable();
            $table->string('grade', 50)->nullable();
            $table->string('viscosity', 50)->nullable();
            $table->decimal('current_stock', 10, 2)->default(0.00);
            $table->decimal('minimum_stock', 8, 2)->default(0.00);
            $table->decimal('maximum_stock', 10, 2)->nullable();
            $table->string('unit_of_measure', 20)->default('Liters');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('supplier_name', 200)->nullable();
            $table->date('last_received_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('storage_location')->nullable();
            $table->text('specifications')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('product_type', 'idx_product_type');
            $table->index('brand', 'idx_brand');
            $table->index('current_stock', 'idx_current_stock');
            $table->index('minimum_stock', 'idx_minimum_stock');
            $table->index('expiry_date', 'idx_expiry_date');
            $table->index('supplier_name', 'idx_supplier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_lubricant_stock');
    }
};
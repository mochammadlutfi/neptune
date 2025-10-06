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
        Schema::create('fuel_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_tank_id')->constrained('fuel_tanks')->onDelete('cascade');
            $table->date('transaction_date');
            $table->enum('transaction_type', ['Receipt', 'Consumption', 'Transfer', 'Adjustment']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->string('supplier_name', 200)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('transaction_description')->nullable();
            $table->decimal('tank_level_before', 10, 2)->nullable();
            $table->decimal('tank_level_after', 10, 2)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('transaction_date', 'idx_transaction_date');
            $table->index('transaction_type', 'idx_transaction_type');
            $table->index('supplier_name', 'idx_supplier_name');
            $table->index('reference_number', 'idx_reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_inventory');
    }
};
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
        Schema::create('fpu_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('operation_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->decimal('oil_production', 10, 2)->default(0.00);
            $table->decimal('gas_production', 10, 2)->default(0.00);
            $table->decimal('water_production', 10, 2)->default(0.00);
            $table->decimal('oil_export', 10, 2)->default(0.00);
            $table->decimal('gas_export', 10, 2)->default(0.00);
            $table->decimal('flare_gas', 8, 2)->default(0.00);
            $table->decimal('fuel_gas', 8, 2)->default(0.00);
            $table->decimal('separator_pressure', 8, 2)->nullable();
            $table->decimal('separator_temperature', 8, 2)->nullable();
            $table->decimal('manifold_pressure', 8, 2)->nullable();
            $table->text('operational_issues')->nullable();
            $table->text('maintenance_performed')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'operation_date', 'shift'], 'unique_fpu_operation');

            // Indexes
            $table->index('operation_date', 'idx_operation_date');
            $table->index('shift', 'idx_shift');
            $table->index(['oil_production', 'gas_production'], 'idx_production');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fpu_operations');
    }
};
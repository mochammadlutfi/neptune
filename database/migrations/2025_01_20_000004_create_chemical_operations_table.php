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
        Schema::create('chemical_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->foreignId('chemical_id')->constrained('chemicals')->onDelete('cascade');
            $table->date('operation_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->decimal('quantity_used_liters', 10, 2)->default(0.00);
            $table->decimal('stock_beginning_liters', 10, 2)->default(0.00);
            $table->decimal('stock_received_liters', 10, 2)->default(0.00);
            $table->decimal('stock_ending_liters', 10, 2)->default(0.00);
            $table->string('injection_point', 100)->nullable();
            $table->decimal('injection_rate_lph', 8, 2)->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'chemical_id', 'operation_date', 'shift'], 'unique_chemical_operation');

            // Indexes
            $table->index('operation_date', 'idx_operation_date');
            $table->index('shift', 'idx_shift');
            $table->index('injection_point', 'idx_injection_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemical_operations');
    }
};
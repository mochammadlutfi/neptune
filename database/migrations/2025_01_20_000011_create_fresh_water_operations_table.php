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
        Schema::create('fresh_water_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('operation_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->decimal('water_produced', 10, 2)->default(0.00);
            $table->decimal('water_consumed', 10, 2)->default(0.00);
            $table->decimal('tank_level_start', 8, 2)->nullable();
            $table->decimal('tank_level_end', 8, 2)->nullable();
            $table->decimal('salinity_level', 8, 2)->nullable();
            $table->decimal('chlorine_level', 8, 2)->nullable();
            $table->enum('system_status', ['Running', 'Standby', 'Maintenance', 'Fault'])->default('Running');
            $table->text('maintenance_performed')->nullable();
            $table->text('issues_encountered')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'operation_date', 'shift'], 'unique_fresh_water_operation');

            // Indexes
            $table->index('operation_date', 'idx_operation_date');
            $table->index('shift', 'idx_shift');
            $table->index('system_status', 'idx_system_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fresh_water_operations');
    }
};
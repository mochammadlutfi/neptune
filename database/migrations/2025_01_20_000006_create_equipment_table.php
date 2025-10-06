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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('equipment_code', 50)->unique();
            $table->string('equipment_name', 200);
            $table->enum('equipment_type', ['Engine', 'Generator', 'Compressor', 'Pump', 'Separator', 'Heat Exchanger', 'Valve', 'Instrumentation', 'Safety System', 'Other']);
            $table->string('manufacturer', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->year('manufacture_year')->nullable();
            $table->date('installation_date')->nullable();
            $table->decimal('rated_capacity', 12, 2)->nullable();
            $table->string('capacity_unit', 20)->nullable();
            $table->enum('status', ['Operational', 'Standby', 'Maintenance', 'Out of Service', 'Decommissioned'])->default('Operational');
            $table->text('specifications')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vessel_id', 'idx_vessel_id');
            $table->index('equipment_type', 'idx_equipment_type');
            $table->index('status', 'idx_status');
            $table->index('manufacturer', 'idx_manufacturer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
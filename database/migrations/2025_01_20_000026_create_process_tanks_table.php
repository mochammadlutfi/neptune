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
        Schema::create('process_tanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('tank_name', 100);
            $table->enum('tank_type', ['Separator', 'Storage', 'Test', 'Slop', 'Produced Water', 'Other']);
            $table->enum('fluid_type', ['Oil', 'Gas', 'Water', 'Mixed', 'Chemical']);
            $table->decimal('capacity', 12, 2);
            $table->decimal('working_capacity', 12, 2)->nullable();
            $table->decimal('current_level', 10, 2)->default(0.00);
            $table->decimal('operating_pressure', 8, 2)->nullable();
            $table->decimal('operating_temperature', 8, 2)->nullable();
            $table->decimal('design_pressure', 8, 2)->nullable();
            $table->decimal('design_temperature', 8, 2)->nullable();
            $table->enum('tank_status', ['Active', 'Inactive', 'Maintenance', 'Testing'])->default('Active');
            $table->string('tank_location', 200)->nullable();
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->text('safety_features')->nullable();
            $table->text('specifications')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'tank_name'], 'unique_vessel_process_tank');

            // Indexes
            $table->index('tank_type', 'idx_tank_type');
            $table->index('fluid_type', 'idx_fluid_type');
            $table->index('tank_status', 'idx_tank_status');
            $table->index('current_level', 'idx_current_level');
            $table->index('next_inspection_date', 'idx_next_inspection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_tanks');
    }
};
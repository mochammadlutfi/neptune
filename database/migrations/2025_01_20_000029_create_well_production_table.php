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
        Schema::create('well_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');
            $table->string('well_name', 100);
            $table->string('well_number', 50);
            $table->date('production_date');
            $table->enum('shift', ['Day', 'Night', 'Full Day'])->default('Full Day');
            $table->decimal('oil_production', 10, 2)->default(0.00); // in barrels
            $table->decimal('gas_production', 12, 2)->default(0.00); // in MSCF
            $table->decimal('water_production', 10, 2)->default(0.00); // in barrels
            $table->decimal('total_fluid', 10, 2)->default(0.00); // in barrels
            $table->decimal('water_cut', 5, 2)->default(0.00); // percentage
            $table->decimal('gor', 8, 2)->nullable(); // Gas Oil Ratio
            $table->decimal('wellhead_pressure', 8, 2)->nullable(); // in psi
            $table->decimal('wellhead_temperature', 6, 2)->nullable(); // in Celsius
            $table->decimal('choke_size', 6, 2)->nullable(); // in inches
            $table->decimal('flowing_tubing_pressure', 8, 2)->nullable(); // in psi
            $table->decimal('flowing_casing_pressure', 8, 2)->nullable(); // in psi
            $table->decimal('separator_pressure', 8, 2)->nullable(); // in psi
            $table->decimal('separator_temperature', 6, 2)->nullable(); // in Celsius
            $table->enum('well_status', ['Producing', 'Shut-in', 'Testing', 'Maintenance', 'Workover', 'Abandoned'])->default('Producing');
            $table->integer('runtime_hours')->default(24); // hours in operation
            $table->decimal('downtime_hours', 4, 2)->default(0.00);
            $table->text('downtime_reason')->nullable();
            $table->text('production_issues')->nullable();
            $table->text('maintenance_activities')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('validated_at')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'well_name', 'production_date', 'shift'], 'unique_well_production');

            // Indexes
            $table->index('production_date', 'idx_production_date');
            $table->index(['vessel_id', 'well_name'], 'idx_vessel_well');
            $table->index('well_number', 'idx_well_number');
            $table->index('shift', 'idx_shift');
            $table->index('well_status', 'idx_well_status');
            $table->index('oil_production', 'idx_oil_production');
            $table->index('gas_production', 'idx_gas_production');
            $table->index('water_cut', 'idx_water_cut');
            $table->index('is_validated', 'idx_is_validated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('well_production');
    }
};
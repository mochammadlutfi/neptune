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
        Schema::create('tank_level_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');
            $table->string('tank_name', 100);
            $table->enum('tank_type', ['Fuel', 'Fresh Water', 'Process', 'Ballast', 'Waste', 'Chemical', 'Other']);
            $table->datetime('reading_datetime');
            $table->decimal('current_level', 8, 2); // in meters or percentage
            $table->decimal('capacity', 10, 2); // total capacity
            $table->decimal('volume', 10, 2)->nullable(); // calculated volume
            $table->decimal('percentage', 5, 2)->nullable(); // percentage full
            $table->decimal('temperature', 6, 2)->nullable(); // in Celsius
            $table->decimal('density', 8, 4)->nullable(); // specific gravity
            $table->decimal('pressure', 8, 2)->nullable(); // in bar
            $table->string('unit_of_measure', 20)->default('Meters');
            $table->enum('reading_method', ['Manual', 'Automatic', 'Ultrasonic', 'Radar', 'Float'])->default('Manual');
            $table->string('instrument_used', 100)->nullable();
            $table->decimal('ullage', 8, 2)->nullable(); // empty space from top
            $table->enum('tank_status', ['Normal', 'Low Level', 'High Level', 'Critical', 'Overflow Risk', 'Maintenance'])->default('Normal');
            $table->text('observations')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('validated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('reading_datetime', 'idx_reading_datetime');
            $table->index(['vessel_id', 'tank_name'], 'idx_vessel_tank');
            $table->index('tank_type', 'idx_tank_type');
            $table->index('tank_status', 'idx_tank_status');
            $table->index('reading_method', 'idx_reading_method');
            $table->index('current_level', 'idx_current_level');
            $table->index('percentage', 'idx_percentage');
            $table->index('is_validated', 'idx_is_validated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tank_level_readings');
    }
};
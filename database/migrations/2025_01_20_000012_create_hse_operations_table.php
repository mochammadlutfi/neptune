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
        Schema::create('hse_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('operation_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->integer('personnel_onboard')->default(0);
            $table->integer('safety_meetings_conducted')->default(0);
            $table->integer('safety_inspections')->default(0);
            $table->integer('near_miss_reports')->default(0);
            $table->integer('incidents_reported')->default(0);
            $table->integer('drills_conducted')->default(0);
            $table->enum('weather_condition', ['Good', 'Fair', 'Poor', 'Severe'])->default('Good');
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->decimal('wave_height', 5, 2)->nullable();
            $table->decimal('visibility', 5, 2)->nullable();
            $table->text('safety_observations')->nullable();
            $table->text('environmental_observations')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'operation_date', 'shift'], 'unique_hse_operation');

            // Indexes
            $table->index('operation_date', 'idx_operation_date');
            $table->index('shift', 'idx_shift');
            $table->index('weather_condition', 'idx_weather_condition');
            $table->index(['incidents_reported', 'near_miss_reports'], 'idx_safety_reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hse_operations');
    }
};
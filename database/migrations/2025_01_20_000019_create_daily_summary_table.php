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
        Schema::create('daily_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('summary_date');
            $table->decimal('total_oil_production', 12, 2)->default(0.00);
            $table->decimal('total_gas_production', 12, 2)->default(0.00);
            $table->decimal('total_water_production', 12, 2)->default(0.00);
            $table->decimal('total_oil_export', 12, 2)->default(0.00);
            $table->decimal('total_gas_export', 12, 2)->default(0.00);
            $table->decimal('total_flare_gas', 10, 2)->default(0.00);
            $table->decimal('total_fuel_consumption', 10, 2)->default(0.00);
            $table->integer('total_personnel', false, true)->default(0);
            $table->integer('safety_incidents', false, true)->default(0);
            $table->integer('equipment_downtime_hours', false, true)->default(0);
            $table->enum('weather_condition', ['Good', 'Fair', 'Poor', 'Severe'])->default('Good');
            $table->text('operational_highlights')->nullable();
            $table->text('safety_observations')->nullable();
            $table->text('maintenance_summary')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'summary_date'], 'unique_daily_summary');

            // Indexes
            $table->index('summary_date', 'idx_summary_date');
            $table->index(['total_oil_production', 'total_gas_production'], 'idx_production_totals');
            $table->index('safety_incidents', 'idx_safety_incidents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_summary');
    }
};
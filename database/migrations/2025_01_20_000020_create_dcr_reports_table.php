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
        Schema::create('dcr_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('report_date');
            $table->string('report_number', 50)->unique();
            $table->enum('shift', ['Day', 'Night']);
            $table->decimal('oil_production', 10, 2)->default(0.00);
            $table->decimal('gas_production', 10, 2)->default(0.00);
            $table->decimal('water_production', 10, 2)->default(0.00);
            $table->decimal('oil_export', 10, 2)->default(0.00);
            $table->decimal('gas_export', 10, 2)->default(0.00);
            $table->decimal('flare_gas', 8, 2)->default(0.00);
            $table->decimal('fuel_consumption', 8, 2)->default(0.00);
            $table->integer('personnel_onboard')->default(0);
            $table->text('operational_activities')->nullable();
            $table->text('maintenance_activities')->nullable();
            $table->text('safety_observations')->nullable();
            $table->text('environmental_observations')->nullable();
            $table->text('weather_conditions')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('report_status', ['Draft', 'Submitted', 'Approved', 'Rejected'])->default('Draft');
            $table->foreignId('prepared_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('report_date', 'idx_report_date');
            $table->index('report_number', 'idx_report_number');
            $table->index('shift', 'idx_shift');
            $table->index('report_status', 'idx_report_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dcr_reports');
    }
};
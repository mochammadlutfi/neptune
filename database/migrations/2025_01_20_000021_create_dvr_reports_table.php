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
        Schema::create('dvr_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('report_date');
            $table->string('report_number', 50)->unique();
            $table->integer('vessel_arrivals')->default(0);
            $table->integer('vessel_departures')->default(0);
            $table->decimal('cargo_loaded', 12, 2)->default(0.00);
            $table->decimal('cargo_discharged', 12, 2)->default(0.00);
            $table->enum('sea_condition', ['Calm', 'Slight', 'Moderate', 'Rough', 'Very Rough'])->default('Calm');
            $table->decimal('tide_height', 5, 2)->nullable();
            $table->decimal('current_speed', 5, 2)->nullable();
            $table->string('current_direction', 10)->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->string('wind_direction', 10)->nullable();
            $table->decimal('visibility', 5, 2)->nullable();
            $table->text('vessel_movements')->nullable();
            $table->text('port_activities')->nullable();
            $table->text('marine_incidents')->nullable();
            $table->text('navigation_warnings')->nullable();
            $table->text('weather_observations')->nullable();
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
            $table->index('sea_condition', 'idx_sea_condition');
            $table->index('report_status', 'idx_report_status');
            $table->index(['vessel_arrivals', 'vessel_departures'], 'idx_vessel_movements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dvr_reports');
    }
};
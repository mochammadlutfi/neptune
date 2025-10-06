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
        Schema::create('marine_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('operation_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->integer('vessel_arrivals')->default(0);
            $table->integer('vessel_departures')->default(0);
            $table->decimal('cargo_loaded', 12, 2)->default(0.00);
            $table->decimal('cargo_discharged', 12, 2)->default(0.00);
            $table->enum('sea_condition', ['Calm', 'Slight', 'Moderate', 'Rough', 'Very Rough'])->default('Calm');
            $table->decimal('tide_height', 5, 2)->nullable();
            $table->decimal('current_speed', 5, 2)->nullable();
            $table->string('current_direction', 10)->nullable();
            $table->enum('berth_status', ['Available', 'Occupied', 'Maintenance', 'Restricted'])->default('Available');
            $table->text('navigation_warnings')->nullable();
            $table->text('port_activities')->nullable();
            $table->text('marine_incidents')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'operation_date', 'shift'], 'unique_marine_operation');

            // Indexes
            $table->index('operation_date', 'idx_operation_date');
            $table->index('shift', 'idx_shift');
            $table->index('sea_condition', 'idx_sea_condition');
            $table->index('berth_status', 'idx_berth_status');
            $table->index(['vessel_arrivals', 'vessel_departures'], 'idx_vessel_movements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marine_operations');
    }
};
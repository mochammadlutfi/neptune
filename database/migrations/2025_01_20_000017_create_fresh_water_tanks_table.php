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
        Schema::create('fresh_water_tanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('tank_name', 100);
            $table->enum('tank_type', ['Storage', 'Service', 'Emergency', 'Potable']);
            $table->decimal('capacity', 10, 2);
            $table->decimal('current_level', 10, 2)->default(0.00);
            $table->decimal('minimum_level', 8, 2)->default(0.00);
            $table->decimal('maximum_level', 10, 2);
            $table->enum('tank_status', ['Active', 'Inactive', 'Maintenance', 'Empty'])->default('Active');
            $table->decimal('salinity_level', 8, 2)->nullable();
            $table->decimal('chlorine_level', 8, 2)->nullable();
            $table->date('last_cleaning_date')->nullable();
            $table->text('tank_location')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'tank_name'], 'unique_vessel_water_tank');

            // Indexes
            $table->index('tank_type', 'idx_tank_type');
            $table->index('tank_status', 'idx_tank_status');
            $table->index('current_level', 'idx_current_level');
            $table->index('last_cleaning_date', 'idx_last_cleaning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fresh_water_tanks');
    }
};
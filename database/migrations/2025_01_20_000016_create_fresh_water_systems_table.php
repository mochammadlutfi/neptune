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
        Schema::create('fresh_water_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('system_name', 100);
            $table->enum('system_type', ['RO Plant', 'Evaporator', 'Storage', 'Distribution']);
            $table->decimal('capacity', 10, 2);
            $table->decimal('production_rate', 8, 2)->nullable();
            $table->enum('system_status', ['Running', 'Standby', 'Maintenance', 'Fault'])->default('Running');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('system_location')->nullable();
            $table->text('specifications')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'system_name'], 'unique_vessel_system');

            // Indexes
            $table->index('system_type', 'idx_system_type');
            $table->index('system_status', 'idx_system_status');
            $table->index('next_maintenance_date', 'idx_next_maintenance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fresh_water_systems');
    }
};
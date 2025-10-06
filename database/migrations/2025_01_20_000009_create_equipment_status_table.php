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
        Schema::create('equipment_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->date('status_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->enum('operational_status', ['Running', 'Standby', 'Stopped', 'Maintenance', 'Fault']);
            $table->decimal('runtime_hours', 8, 2)->default(0.00);
            $table->decimal('load_percentage', 5, 2)->nullable();
            $table->text('fault_description')->nullable();
            $table->text('maintenance_action')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['equipment_id', 'status_date', 'shift'], 'unique_equipment_status');

            // Indexes
            $table->index('status_date', 'idx_status_date');
            $table->index('operational_status', 'idx_operational_status');
            $table->index('shift', 'idx_shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_status');
    }
};
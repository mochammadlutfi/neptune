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
        Schema::create('maintenance_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('work_order_number', 100)->nullable();
            $table->enum('maintenance_type', ['Preventive', 'Corrective', 'Predictive', 'Emergency', 'Overhaul']);
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->string('maintenance_description', 500);
            $table->text('work_performed')->nullable();
            $table->text('parts_used')->nullable();
            $table->decimal('labor_hours', 8, 2)->default(0.00);
            $table->decimal('material_cost', 12, 2)->default(0.00);
            $table->decimal('labor_cost', 12, 2)->default(0.00);
            $table->decimal('total_cost', 12, 2)->default(0.00);
            $table->string('technician_name', 100)->nullable();
            $table->string('supervisor_name', 100)->nullable();
            $table->enum('status', ['Planned', 'In Progress', 'Completed', 'Cancelled', 'On Hold'])->default('Planned');
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('maintenance_date', 'idx_maintenance_date');
            $table->index('work_order_number', 'idx_work_order');
            $table->index('maintenance_type', 'idx_maintenance_type');
            $table->index('priority', 'idx_priority');
            $table->index('status', 'idx_status');
            $table->index('next_maintenance_date', 'idx_next_maintenance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_activities');
    }
};
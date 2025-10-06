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
        Schema::create('equipment_fuel_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->date('consumption_date');
            $table->enum('shift', ['Day', 'Night']);
            $table->decimal('fuel_consumed_liters', 10, 2)->default(0.00);
            $table->decimal('runtime_hours', 8, 2)->default(0.00);
            $table->decimal('load_percentage', 5, 2)->nullable();
            $table->decimal('fuel_rate_lph', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint
            $table->unique(['equipment_id', 'consumption_date', 'shift'], 'unique_equipment_fuel_consumption');

            // Indexes
            $table->index('consumption_date', 'idx_consumption_date');
            $table->index('shift', 'idx_shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_fuel_consumption');
    }
};
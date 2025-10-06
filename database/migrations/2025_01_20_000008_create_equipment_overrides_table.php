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
        Schema::create('equipment_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->timestamp('override_start');
            $table->timestamp('override_end')->nullable();
            $table->enum('override_type', ['Manual Start', 'Manual Stop', 'Safety Override', 'Maintenance Override', 'Emergency Stop']);
            $table->text('reason');
            $table->string('authorized_by', 100);
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('equipment_id', 'idx_equipment_id');
            $table->index('override_start', 'idx_override_start');
            $table->index('override_type', 'idx_override_type');
            $table->index('is_active', 'idx_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_overrides');
    }
};
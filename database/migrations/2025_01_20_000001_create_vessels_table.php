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
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('type', ['MOPU', 'FPU', 'FPSO', 'Platform']);
            $table->string('operator', 100)->nullable();
            $table->string('field_name', 100)->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Maintenance', 'Decommissioned'])->default('Active');
            $table->timestamps();

            // Indexes
            $table->index('code', 'idx_code');
            $table->index('status', 'idx_status');
            $table->index('field_name', 'idx_field_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
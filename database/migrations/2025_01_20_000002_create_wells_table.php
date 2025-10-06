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
        Schema::create('wells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('type', ['Oil', 'Gas', 'Gas Lift', 'Water Injection', 'Gas Injection']);
            $table->decimal('max_oil_rate', 10, 2)->nullable();
            $table->decimal('max_gas_rate', 10, 2)->nullable();
            $table->decimal('max_water_rate', 10, 2)->nullable();
            $table->enum('status', ['Active', 'Shut-in', 'Abandoned', 'Suspended', 'Testing', 'Workover'])->default('Active');
            $table->timestamps();

            // Indexes
            $table->index('vessel_id', 'idx_vessel_id');
            $table->index('code', 'idx_code');
            $table->index('status', 'idx_status');
            $table->index('type', 'idx_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wells');
    }
};
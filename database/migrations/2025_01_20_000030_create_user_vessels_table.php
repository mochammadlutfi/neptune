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
        Schema::create('user_vessels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->enum('role', ['Captain', 'Chief Officer', 'Engineer', 'Operator', 'Supervisor', 'Technician', 'Safety Officer', 'Observer', 'Other'])->default('Operator');
            $table->enum('access_level', ['Read Only', 'Read Write', 'Admin', 'Full Access'])->default('Read Only');
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Transferred'])->default('Active');
            $table->text('responsibilities')->nullable();
            $table->text('permissions')->nullable(); // JSON string for specific permissions
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint - one active assignment per user per vessel
            $table->unique(['user_id', 'vessel_id', 'status'], 'unique_active_user_vessel');

            // Indexes
            $table->index(['user_id', 'vessel_id'], 'idx_user_vessel');
            $table->index('role', 'idx_role');
            $table->index('access_level', 'idx_access_level');
            $table->index('assigned_date', 'idx_assigned_date');
            $table->index('status', 'idx_status');
            $table->index('assigned_by', 'idx_assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vessels');
    }
};
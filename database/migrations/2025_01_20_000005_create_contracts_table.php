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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('contract_number', 50)->unique();
            $table->string('contract_name', 200);
            $table->enum('contract_type', ['Service', 'Supply', 'Maintenance', 'Charter', 'Other']);
            $table->string('contractor_name', 200);
            $table->text('contract_description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['Active', 'Completed', 'Terminated', 'Suspended'])->default('Active');
            $table->text('terms_conditions')->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('vessel_id', 'idx_vessel_id');
            $table->index('contract_type', 'idx_contract_type');
            $table->index('status', 'idx_status');
            $table->index('contractor_name', 'idx_contractor_name');
            $table->index(['start_date', 'end_date'], 'idx_contract_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
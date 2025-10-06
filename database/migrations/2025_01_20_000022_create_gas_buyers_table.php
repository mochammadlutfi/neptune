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
        Schema::create('gas_buyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('buyer_name', 200);
            $table->string('buyer_code', 50)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('country', 100)->nullable();
            $table->enum('buyer_type', ['Direct', 'Trader', 'End User', 'Distributor']);
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->integer('payment_terms_days')->default(30);
            $table->enum('payment_method', ['Bank Transfer', 'Letter of Credit', 'Cash', 'Other']);
            $table->string('bank_details', 500)->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Suspended'])->default('Active');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->text('special_terms')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['vessel_id', 'buyer_name'], 'unique_vessel_buyer');

            // Indexes
            $table->index('buyer_name', 'idx_buyer_name');
            $table->index('buyer_code', 'idx_buyer_code');
            $table->index('buyer_type', 'idx_buyer_type');
            $table->index('status', 'idx_status');
            $table->index('contract_end_date', 'idx_contract_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_buyers');
    }
};
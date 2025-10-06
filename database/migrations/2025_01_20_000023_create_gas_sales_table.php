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
        Schema::create('gas_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->foreignId('gas_buyer_id')->constrained('gas_buyers')->onDelete('cascade');
            $table->date('sale_date');
            $table->string('invoice_number', 100)->unique();
            $table->string('delivery_order_number', 100)->nullable();
            $table->decimal('quantity_sold', 12, 2);
            $table->string('unit_of_measure', 20)->default('MSCF');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->enum('delivery_method', ['Pipeline', 'Truck', 'Ship', 'Other']);
            $table->string('delivery_location', 200)->nullable();
            $table->enum('payment_status', ['Pending', 'Partial', 'Paid', 'Overdue'])->default('Pending');
            $table->date('payment_due_date')->nullable();
            $table->date('payment_received_date')->nullable();
            $table->decimal('payment_received_amount', 15, 2)->default(0.00);
            $table->text('delivery_notes')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('sale_date', 'idx_sale_date');
            $table->index('invoice_number', 'idx_invoice_number');
            $table->index('delivery_order_number', 'idx_delivery_order');
            $table->index('payment_status', 'idx_payment_status');
            $table->index('payment_due_date', 'idx_payment_due');
            $table->index(['quantity_sold', 'total_amount'], 'idx_sales_metrics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_sales');
    }
};
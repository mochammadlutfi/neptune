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
        Schema::create('lab_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->date('analysis_date');
            $table->string('sample_id', 100)->unique();
            $table->enum('sample_type', ['Oil', 'Gas', 'Water', 'Chemical', 'Fuel', 'Other']);
            $table->string('sample_source', 200)->nullable();
            $table->datetime('sample_collection_time');
            $table->enum('analysis_type', ['Routine', 'Special', 'Compliance', 'Quality Control']);
            $table->string('laboratory_name', 200)->nullable();
            $table->string('analyst_name', 100)->nullable();
            $table->json('test_parameters')->nullable(); // Store test parameters as JSON
            $table->json('test_results')->nullable(); // Store test results as JSON
            $table->json('specifications')->nullable(); // Store specifications as JSON
            $table->enum('compliance_status', ['Pass', 'Fail', 'Conditional', 'Pending'])->default('Pending');
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->date('report_date')->nullable();
            $table->string('report_number', 100)->nullable();
            $table->enum('report_status', ['Draft', 'Final', 'Approved'])->default('Draft');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('analysis_date', 'idx_analysis_date');
            $table->index('sample_id', 'idx_sample_id');
            $table->index('sample_type', 'idx_sample_type');
            $table->index('analysis_type', 'idx_analysis_type');
            $table->index('compliance_status', 'idx_compliance_status');
            $table->index('report_status', 'idx_report_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_analysis');
    }
};
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json', 'array', 'date', 'datetime'])->default('string');
            $table->string('category', 50)->default('general');
            $table->string('label', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('validation_rules')->nullable();
            $table->text('default_value')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by frontend
            $table->boolean('is_editable')->default(true); // Can be modified via UI
            $table->boolean('requires_restart')->default(false); // Requires app restart after change
            $table->integer('sort_order')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('key', 'idx_key');
            $table->index('category', 'idx_category');
            $table->index('type', 'idx_type');
            $table->index('is_public', 'idx_is_public');
            $table->index('is_editable', 'idx_is_editable');
            $table->index('sort_order', 'idx_sort_order');
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Neptune FPSO Management System',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of the application displayed in the UI',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'company_name',
                'value' => 'Neptune Energy',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Company Name',
                'description' => 'The company name for reports and documentation',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_timezone',
                'value' => 'UTC',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Default Timezone',
                'description' => 'Default timezone for the application',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Date Format',
                'description' => 'Default date format for display',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'datetime_format',
                'value' => 'Y-m-d H:i:s',
                'type' => 'string',
                'category' => 'general',
                'label' => 'DateTime Format',
                'description' => 'Default datetime format for display',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'string',
                'category' => 'financial',
                'label' => 'Default Currency',
                'description' => 'Default currency for financial calculations',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'oil_unit',
                'value' => 'Barrels',
                'type' => 'string',
                'category' => 'production',
                'label' => 'Oil Production Unit',
                'description' => 'Default unit for oil production measurements',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'gas_unit',
                'value' => 'MSCF',
                'type' => 'string',
                'category' => 'production',
                'label' => 'Gas Production Unit',
                'description' => 'Default unit for gas production measurements',
                'is_public' => true,
                'is_editable' => true,
                'sort_order' => 21,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
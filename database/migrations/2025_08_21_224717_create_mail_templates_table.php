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
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->comment('Kode unik template');
            $table->string('name', 150)->comment('Nama template email');
            $table->string('subject', 200)->comment('Subject email');
            $table->text('body')->comment('Isi template email');
            $table->json('variables')->nullable()->comment('Variables yang tersedia dalam template');
            $table->text('description')->nullable()->comment('Deskripsi template');
            $table->boolean('is_active')->default(true)->comment('Status aktif template');
            $table->timestamps();
            
            // Index untuk performa
            $table->unique('code');
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};

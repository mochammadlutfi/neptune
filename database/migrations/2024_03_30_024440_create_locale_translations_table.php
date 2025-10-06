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
        Schema::create('locale_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locale_id');
            $table->string('group');
            $table->string('key');
            $table->string('value');
            $table->timestamps();

            
            $table->foreign('locale_id')->references('id')->on('locales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locale_translations');
    }
};

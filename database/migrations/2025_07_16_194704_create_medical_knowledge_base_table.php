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
        Schema::create('medical_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // symptoms, diseases, treatments, procedures, etc.
            $table->string('title');
            $table->text('description');
            $table->json('keywords'); // Searchable keywords
            $table->text('detailed_info');
            $table->string('severity_level')->nullable(); // low, medium, high, emergency
            $table->boolean('requires_doctor')->default(false);
            $table->text('recommendations')->nullable();
            $table->json('related_topics')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('category');
            $table->index('severity_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_knowledge_base');
    }
};

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
        Schema::create('chatbot_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->integer('rating')->unsigned()->comment('1-5 rating');
            $table->text('feedback')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('message_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_feedback');
    }
};

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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->text('user_message');
            $table->text('bot_response');
            $table->json('context')->nullable(); // Store conversation context
            $table->string('intent')->nullable(); // Detected intent
            $table->decimal('confidence', 3, 2)->nullable(); // Confidence score
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};

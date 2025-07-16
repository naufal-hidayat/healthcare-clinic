<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_message',
        'bot_response',
        'context',
        'intent',
        'confidence',
        'ip_address'
    ];

    protected $casts = [
        'context' => 'array',
        'confidence' => 'decimal:2',
    ];
}
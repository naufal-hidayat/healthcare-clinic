<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('api')->group(function () {
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
});

Route::prefix('chatbot')->group(function () {
    Route::post('/chat', [ChatbotController::class, 'chat'])->name('api.chatbot.chat');
    Route::post('/history', [ChatbotController::class, 'getHistory'])->name('api.chatbot.history');
    Route::get('/suggestions', [ChatbotController::class, 'getSuggestions'])->name('api.chatbot.suggestions');
    Route::post('/feedback', [ChatbotController::class, 'feedback'])->name('api.chatbot.feedback');
});
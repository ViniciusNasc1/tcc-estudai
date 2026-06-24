<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Rota raiz: redireciona para login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas do Estudante (autenticadas)
Route::middleware('auth')->group(function () {
    Route::get('/home', [StudentController::class, 'home'])->name('home');
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/assistant', [StudentController::class, 'assistant'])->name('assistant');
    Route::post('/assistant/message', [StudentController::class, 'message'])->name('assistant.message');

    // Quizzes
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
});

// Rotas do Administrador
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', [AdminController::class, 'home'])->name('home');
});

require __DIR__.'/auth.php';

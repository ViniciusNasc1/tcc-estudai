<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Rota raiz: redireciona para login
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Rotas do Estudante (autenticadas) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/home',      [StudentController::class, 'home'])->name('home');
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/assistant', [StudentController::class, 'assistant'])->name('assistant');
    Route::post('/assistant/message', [StudentController::class, 'message'])->name('assistant.message');

    // Tópicos
    Route::get('/topicos',         [StudentController::class, 'topicos'])->name('topicos');
    Route::get('/topicos/{topic}', [StudentController::class, 'showTopico'])->name('topico.show');

    // Histórico e Recomendações
    Route::get('/historico-quizzes', [StudentController::class, 'historicoQuizzes'])->name('historico.quizzes');
    Route::get('/recomendacoes',     [StudentController::class, 'recomendacoes'])->name('recomendacoes');

    // Trilha de Estudo
    Route::get('/trilha', [StudentController::class, 'trilha'])->name('trilha');

    // Quizzes
    Route::get('/quiz/{quiz}',          [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/submit',  [QuizController::class, 'submit'])->name('quiz.submit');
});

// ─── Rotas do Administrador ───────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard/Home
    Route::get('/home', [AdminController::class, 'home'])->name('home');

    // Relatórios
    Route::get('/relatorios',        [AdminController::class, 'relatorios'])->name('relatorios');
    Route::post('/relatorios/gerar', [AdminController::class, 'gerarRelatorio'])->name('relatorios.gerar');

    // Histórico de atividades
    Route::get('/historico', [AdminController::class, 'historico'])->name('historico');

    // Usuários
    Route::get('/usuarios',              [AdminController::class, 'usuarios'])->name('usuarios');
    Route::get('/usuarios/{user}/edit',  [AdminController::class, 'editUsuario'])->name('usuarios.edit');
    Route::put('/usuarios/{user}',       [AdminController::class, 'updateUsuario'])->name('usuarios.update');
    Route::delete('/usuarios/{user}',    [AdminController::class, 'destroyUsuario'])->name('usuarios.destroy');

    // Tópicos
    Route::get('/topicos', [AdminController::class, 'topicos'])->name('topicos');
    Route::post('/topicos', [AdminController::class, 'storeTopico'])->name('topicos.store');
    Route::put('/topicos/{topic}', [AdminController::class, 'updateTopico'])->name('topicos.update');
    Route::delete('/topicos/{topic}', [AdminController::class, 'destroyTopico'])->name('topicos.destroy');

    // Conteúdos
    Route::get('/conteudos', [AdminController::class, 'conteudos'])->name('conteudos');
    Route::post('/conteudos', [AdminController::class, 'storeConteudo'])->name('conteudos.store');
    Route::put('/conteudos/{content}', [AdminController::class, 'updateConteudo'])->name('conteudos.update');
    Route::delete('/conteudos/{content}', [AdminController::class, 'destroyConteudo'])->name('conteudos.destroy');

    // Quizzes
    Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('quizzes');
    Route::post('/quizzes', [AdminController::class, 'storeQuiz'])->name('quizzes.store');
    Route::put('/quizzes/{quiz}', [AdminController::class, 'updateQuiz'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [AdminController::class, 'destroyQuiz'])->name('quizzes.destroy');

    // Questões (dentro de um quiz)
    Route::post('/quizzes/{quiz}/questoes', [AdminController::class, 'storeQuestao'])->name('questoes.store');
    Route::delete('/questoes/{question}', [AdminController::class, 'destroyQuestao'])->name('questoes.destroy');
});

require __DIR__.'/auth.php';

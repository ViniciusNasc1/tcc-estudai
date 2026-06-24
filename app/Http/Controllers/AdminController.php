<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QuizResult;
use App\Models\Recommendation;
use App\Models\TopicPerformance;
use App\Models\Topic;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Exibe a tela home do administrador com estatísticas globais da plataforma.
     */
    public function home()
    {
        // ─── Métricas Globais ─────────────────────────────────────────────────
        $totalEstudantes = User::where('tipo', 'estudante')->count();

        $totalQuizzes = QuizResult::count();

        $mediaGeralPlataforma = QuizResult::avg('pontuacao_percentual') ?? 0;

        $totalRecomendacoes = Recommendation::count();

        // ─── Desempenho médio por tópico ──────────────────────────────────────
        $topicos = Topic::withCount('quizzes')->get();

        $desempenhoTopicos = TopicPerformance::with('topic')
            ->selectRaw('topic_id, AVG(percentual_acertos) as media_acertos')
            ->groupBy('topic_id')
            ->with('topic')
            ->get();

        // ─── Tabela de Estudantes ─────────────────────────────────────────────
        $estudantes = User::where('tipo', 'estudante')
            ->withCount('quizResults as total_quizzes')
            ->with('quizResults')
            ->get()
            ->map(function ($estudante) {
                $estudante->media_desempenho = $estudante->quizResults->avg('pontuacao_percentual') ?? 0;
                $estudante->recomendacoes_pendentes = Recommendation::where('user_id', $estudante->id)
                    ->where('status', 'pendente')
                    ->count();
                return $estudante;
            });

        return view('admin.home', compact(
            'totalEstudantes',
            'totalQuizzes',
            'mediaGeralPlataforma',
            'totalRecomendacoes',
            'desempenhoTopicos',
            'estudantes'
        ));
    }
}

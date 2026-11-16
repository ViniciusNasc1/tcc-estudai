<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Content;
use App\Models\Question;
use App\Models\Alternative;
use App\Models\QuizResult;
use App\Models\Recommendation;
use App\Models\TopicPerformance;
use App\Models\Relatorio;
use App\Models\HistoricoAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── Home ─────────────────────────────────────────────────────────────────

    public function home()
    {
        $totalEstudantes       = User::where('tipo', 'estudante')->count();
        $totalConteudos        = Content::count();
        $totalQuizzesDisponiveis = Quiz::count();
        $mediaGeralPlataforma  = QuizResult::avg('pontuacao_percentual') ?? 0;
        $totalRecomendacoes    = Recommendation::count();

        $desempenhoTopicos = TopicPerformance::with('topic')
            ->selectRaw('topic_id, AVG(percentual_acertos) as media_acertos')
            ->groupBy('topic_id')
            ->with('topic')
            ->get();

        $estudantes = User::where('tipo', 'estudante')
            ->withCount('quizResults as total_quizzes')
            ->with('quizResults')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($e) {
                $e->media_desempenho       = $e->quizResults->avg('pontuacao_percentual') ?? 0;
                $e->recomendacoes_pendentes = Recommendation::where('user_id', $e->id)->where('status', 'pendente')->count();
                return $e;
            });

        return view('admin.home', compact(
            'totalEstudantes', 'totalConteudos', 'totalQuizzesDisponiveis',
            'mediaGeralPlataforma', 'totalRecomendacoes', 'desempenhoTopicos', 'estudantes'
        ));
    }

    // ─── Relatórios ───────────────────────────────────────────────────────────

    public function relatorios()
    {
        // Desempenho médio por tópico
        $desempenhoTopicos = TopicPerformance::with('topic')
            ->selectRaw('topic_id, AVG(percentual_acertos) as media_acertos, COUNT(*) as total_alunos')
            ->groupBy('topic_id')
            ->get();

        // Todos os estudantes com média
        $estudantes = User::where('tipo', 'estudante')
            ->withCount('quizResults as total_quizzes')
            ->with('quizResults')
            ->get()
            ->map(function ($e) {
                $e->media_desempenho = $e->quizResults->avg('pontuacao_percentual') ?? 0;
                return $e;
            })
            ->sortByDesc('media_desempenho');

        // Evolução geral (média diária)
        $quizzesPorDia = QuizResult::selectRaw("date(created_at) as dia, AVG(pontuacao_percentual) as media")
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $totalQuizzes         = QuizResult::count();
        $mediaGeralPlataforma = QuizResult::avg('pontuacao_percentual') ?? 0;
        $totalEstudantes      = User::where('tipo', 'estudante')->count();
        $totalRecomendacoes   = Recommendation::count();

        // ─── Persiste o relatório no banco (snapshot) ─────────────────────────────
        $admin = Auth::user();
        $relatorio = Relatorio::create([
            'titulo'     => 'Relatório Geral — ' . now()->format('d/m/Y H:i'),
            'tipo'       => 'geral',
            'gerado_por' => $admin->id,
            'dados'      => [
                'total_estudantes'      => $totalEstudantes,
                'total_quizzes'         => $totalQuizzes,
                'media_geral'           => round($mediaGeralPlataforma, 2),
                'total_recomendacoes'   => $totalRecomendacoes,
                'desempenho_por_topico' => $desempenhoTopicos->map(fn($d) => [
                    'topico' => $d->topic->nome ?? '?',
                    'media'  => round($d->media_acertos, 2),
                ])->values(),
                'ranking_estudantes'    => $estudantes->take(10)->map(fn($e) => [
                    'nome'  => $e->name,
                    'media' => round($e->media_desempenho, 2),
                    'quizzes' => $e->total_quizzes,
                ])->values(),
            ],
        ]);

        // ─── Auditoria ────────────────────────────────────────────────────────
        HistoricoAtividade::registrar($admin->id, 'relatorio_gerado', $relatorio->id);

        // Busca histórico de relatórios gerados
        $relatoriosAnteriores = Relatorio::with('gerador')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('admin.relatorios', compact(
            'desempenhoTopicos', 'estudantes', 'quizzesPorDia',
            'totalQuizzes', 'mediaGeralPlataforma', 'totalEstudantes',
            'totalRecomendacoes', 'relatoriosAnteriores'
        ));
    }

    // ─── Usuários ─────────────────────────────────────────────────────────────

    public function usuarios()
    {
        $usuarios = User::withCount('quizResults as total_quizzes')
            ->with('quizResults')
            ->orderBy('tipo')
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                $u->media_desempenho = $u->quizResults->avg('pontuacao_percentual') ?? 0;
                return $u;
            });

        return view('admin.usuarios', compact('usuarios'));
    }

    public function destroyUsuario(User $user)
    {
        if ($user->tipo === 'administrador') {
            return back()->with('error', 'Não é possível excluir um administrador.');
        }
        $user->delete();
        return back()->with('success', 'Usuário excluído com sucesso.');
    }

    public function editUsuario(User $user)
    {
        return view('admin.usuario_edit', compact('user'));
    }

    public function updateUsuario(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'tipo'     => 'required|in:estudante,administrador',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only('name', 'email', 'tipo');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('admin.usuarios')->with('success', "Usuário {$user->name} atualizado com sucesso!");
    }

    // ─── Tópicos ──────────────────────────────────────────────────────────────

    public function topicos()
    {
        $topicos = Topic::withCount(['quizzes', 'contents'])->orderBy('nome')->get();
        return view('admin.topicos', compact('topicos'));
    }

    public function storeTopico(Request $request)
    {
        $request->validate([
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        Topic::create($request->only('nome', 'descricao'));
        return back()->with('success', 'Tópico criado com sucesso!');
    }

    public function updateTopico(Request $request, Topic $topic)
    {
        $request->validate([
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $topic->update($request->only('nome', 'descricao'));
        return back()->with('success', 'Tópico atualizado com sucesso!');
    }

    public function destroyTopico(Topic $topic)
    {
        $topic->delete();
        return back()->with('success', 'Tópico excluído com sucesso!');
    }

    // ─── Conteúdos ────────────────────────────────────────────────────────────

    public function conteudos()
    {
        $conteudos = Content::with('topic')->orderBy('titulo')->get();
        $topicos   = Topic::orderBy('nome')->get();
        return view('admin.conteudos', compact('conteudos', 'topicos'));
    }

    public function storeConteudo(Request $request)
    {
        $request->validate([
            'titulo'   => 'required|string|max:255',
            'descricao'=> 'nullable|string',
            'url'      => 'nullable|url|max:500',
            'topic_id' => 'required|exists:topics,id',
        ]);

        Content::create($request->only('titulo', 'descricao', 'url', 'topic_id'));
        return back()->with('success', 'Conteúdo cadastrado com sucesso!');
    }

    public function updateConteudo(Request $request, Content $content)
    {
        $request->validate([
            'titulo'   => 'required|string|max:255',
            'descricao'=> 'nullable|string',
            'url'      => 'nullable|url|max:500',
            'topic_id' => 'required|exists:topics,id',
        ]);

        $content->update($request->only('titulo', 'descricao', 'url', 'topic_id'));
        return back()->with('success', 'Conteúdo atualizado com sucesso!');
    }

    public function destroyConteudo(Content $content)
    {
        $content->delete();
        return back()->with('success', 'Conteúdo excluído com sucesso!');
    }

    // ─── Quizzes ──────────────────────────────────────────────────────────────

    public function quizzes()
    {
        $quizzes = Quiz::with(['topic', 'questions.alternatives'])->orderBy('titulo')->get();
        $topicos = Topic::orderBy('nome')->get();
        return view('admin.quizzes', compact('quizzes', 'topicos'));
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'titulo'   => 'required|string|max:255',
            'descricao'=> 'nullable|string',
            'topic_id' => 'required|exists:topics,id',
        ]);

        Quiz::create($request->only('titulo', 'descricao', 'topic_id'));
        return back()->with('success', 'Quiz criado com sucesso!');
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $request->validate([
            'titulo'   => 'required|string|max:255',
            'descricao'=> 'nullable|string',
            'topic_id' => 'required|exists:topics,id',
        ]);

        $quiz->update($request->only('titulo', 'descricao', 'topic_id'));
        return back()->with('success', 'Quiz atualizado com sucesso!');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Quiz excluído com sucesso!');
    }

    // ─── Questões ─────────────────────────────────────────────────────────────

    public function storeQuestao(Request $request, Quiz $quiz)
    {
        $request->validate([
            'enunciado'                   => 'required|string',
            'alternativas'                => 'required|array|min:2',
            'alternativas.*.descricao'    => 'required|string',
            'alternativas.*.correta'      => 'nullable',
            'alternativa_correta'         => 'required|integer',
        ]);

        $question = Question::create([
            'enunciado' => $request->enunciado,
            'quiz_id'   => $quiz->id,
        ]);

        foreach ($request->alternativas as $index => $alt) {
            Alternative::create([
                'descricao'   => $alt['descricao'],
                'correta'     => ($index == $request->alternativa_correta),
                'question_id' => $question->id,
            ]);
        }

        return back()->with('success', 'Questão adicionada com sucesso!');
    }

    public function destroyQuestao(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Questão excluída com sucesso!');
    }

    // ─── Histórico de Atividades ───────────────────────────────────────────────

    public function historico()
    {
        $historico = HistoricoAtividade::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.historico', compact('historico'));
    }
}

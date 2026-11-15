<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Recommendation;
use App\Models\QuizResult;
use App\Models\TopicPerformance;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function home()
    {
        $user = Auth::user();

        $topics = Topic::with(['contents', 'quizzes'])->get();

        $performances = TopicPerformance::where('user_id', $user->id)
            ->get()
            ->keyBy('topic_id');

        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->get();

        $latestResults = QuizResult::where('user_id', $user->id)
            ->with('quiz.topic')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('student.home', compact('user', 'topics', 'performances', 'recommendations', 'latestResults'));
    }

    public function topicos()
    {
        $user    = Auth::user();
        $topics  = Topic::with(['contents', 'quizzes'])->get();

        $performances = TopicPerformance::where('user_id', $user->id)
            ->get()
            ->keyBy('topic_id');

        return view('student.topicos', compact('user', 'topics', 'performances'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        $quizzesRealizados = QuizResult::where('user_id', $user->id)->count();

        $mediaGeral = QuizResult::where('user_id', $user->id)->avg('pontuacao_percentual') ?? 0;

        $recomendacoesPendentes = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->count();

        $conteudosVistos = Content::count();

        $topicPerformances = TopicPerformance::where('user_id', $user->id)
            ->with('topic')
            ->get();

        $evolutionResults = QuizResult::where('user_id', $user->id)
            ->with('quiz')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('student.dashboard', compact(
            'quizzesRealizados',
            'mediaGeral',
            'recomendacoesPendentes',
            'conteudosVistos',
            'topicPerformances',
            'evolutionResults'
        ));
    }

    public function assistant(Request $request)
    {
        $user = Auth::user();

        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->get();

        $prefilledRecId = $request->query('rec_id');
        $prefilledRec   = null;
        if ($prefilledRecId) {
            $prefilledRec = Recommendation::where('user_id', $user->id)
                ->with(['content', 'topic'])
                ->find($prefilledRecId);
        }

        return view('student.assistant', compact('recommendations', 'prefilledRec'));
    }

    /**
     * Enviar mensagem ao assistente — integrado com a API do ChatGPT (gpt-4o-mini).
     */
    public function message(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:2000',
            'topic_id' => 'nullable|integer',
        ]);

        $user    = Auth::user();
        $message = trim($request->message);
        $topicId = $request->topic_id;

        // Monta contexto com as recomendações pendentes do estudante
        $recsContext = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->take(5)
            ->get()
            ->map(fn($r) => "- Tópico: {$r->topic->nome} | Conteúdo: {$r->content->titulo} | Motivo: {$r->motivo}")
            ->join("\n");

        // Tópico selecionado (se houver)
        $topicContext = '';
        if ($topicId) {
            $topic = Topic::find($topicId);
            if ($topic) {
                $topicContext = "O estudante está perguntando especificamente sobre o tópico: {$topic->nome}. ";
            }
        }

        // Prompt do sistema — contextualiza o assistente com os dados do aluno
        $systemPrompt = <<<PROMPT
Você é o Assistente Estudaí, uma IA educacional integrada à plataforma Estudaí, especializada em apoiar estudantes de tecnologia e computação.

{$topicContext}

O estudante autenticado chama-se {$user->name} e possui as seguintes recomendações de estudo pendentes geradas automaticamente pelo sistema com base em seu desempenho em quizzes:
{$recsContext}

Sua missão é:
1. Responder às perguntas do estudante de forma clara, didática e objetiva.
2. Usar exemplos de código quando relevante (formatados em blocos de código).
3. Relacionar suas respostas às recomendações pendentes quando for pertinente.
4. Usar linguagem amigável e encorajadora em português brasileiro.
5. Nunca inventar informações falsas — se não souber algo, admita honestamente.

Responda sempre em português brasileiro.
PROMPT;

        $apiKey = env('OPENAI_API_KEY');

        // Se a chave não estiver configurada, retorna aviso amigável
        if (empty($apiKey) || $apiKey === 'sua-chave-aqui') {
            return response()->json([
                'reply' => "⚠️ **Assistente IA não configurado.** Para ativar o ChatGPT, adicione sua chave `OPENAI_API_KEY` no arquivo `.env` do projeto.\n\nEnquanto isso, posso simular: você perguntou sobre **\"{$message}\"**. Consulte o seu material de estudo e os conteúdos recomendados no painel de início!"
            ]);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'max_tokens'  => 1200,
                    'temperature' => 0.7,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $message],
                    ],
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content') ?? 'Desculpe, não consegui gerar uma resposta. Tente novamente.';
            } else {
                $errorMsg = $response->json('error.message') ?? 'Erro desconhecido.';
                $reply    = "❌ Erro ao conectar com a IA: {$errorMsg}";
            }
        } catch (\Exception $e) {
            $reply = "❌ Falha de conexão com o serviço de IA. Verifique sua conexão e tente novamente.";
        }

        return response()->json(['reply' => $reply]);
    }
}

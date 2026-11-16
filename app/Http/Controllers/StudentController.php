<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Recommendation;
use App\Models\QuizResult;
use App\Models\TopicPerformance;
use App\Models\Content;
use App\Models\HistoricoAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    // ─── Home ─────────────────────────────────────────────────────

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

        return view('student.home', compact(
            'user',
            'topics',
            'performances',
            'recommendations',
            'latestResults'
        ));
    }

    // ─── Lista de Tópicos ─────────────────────────────────────────

    public function topicos()
    {
        $user = Auth::user();

        $topics = Topic::with(['contents', 'quizzes'])->get();

        $performances = TopicPerformance::where('user_id', $user->id)
            ->get()
            ->keyBy('topic_id');

        return view('student.topicos', compact(
            'user',
            'topics',
            'performances'
        ));
    }

    // ─── Detalhe de um Tópico ─────────────────────────────────────

    public function showTopico(Topic $topic)
    {
        $user = Auth::user();

        $topic->load(['contents', 'quizzes.questions']);

        $performance = TopicPerformance::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();

        $quizResults = QuizResult::where('user_id', $user->id)
            ->whereIn('quiz_id', $topic->quizzes->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get();

        $topicIcon = match (true) {
            str_contains(strtolower($topic->nome), 'banco') => 'fa-database',
            str_contains(strtolower($topic->nome), 'rede') => 'fa-network-wired',
            str_contains(strtolower($topic->nome), 'progra') => 'fa-code',
            str_contains(strtolower($topic->nome), 'web') => 'fa-globe',
            str_contains(strtolower($topic->nome), 'sist') => 'fa-server',
            str_contains(strtolower($topic->nome), 'algor') => 'fa-diagram-project',
            default => 'fa-book',
        };

        return view('student.topico', compact(
            'topic',
            'performance',
            'quizResults',
            'topicIcon'
        ));
    }

    // ─── Dashboard ───────────────────────────────────────────────

    public function dashboard()
    {
        $user = Auth::user();

        $quizzesRealizados = QuizResult::where('user_id', $user->id)
            ->count();

        $mediaGeral = QuizResult::where('user_id', $user->id)
            ->avg('pontuacao_percentual') ?? 0;

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

    // ─── Histórico de Quizzes ─────────────────────────────────────

    public function historicoQuizzes()
    {
        $user = Auth::user();

        $resultados = QuizResult::where('user_id', $user->id)
            ->with('quiz.topic')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.historico', compact('resultados'));
    }

    // ─── Recomendações ────────────────────────────────────────────

    public function recomendacoes()
    {
        $user = Auth::user();

        $pendentes = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->orderBy('created_at', 'desc')
            ->get();

        $concluidas = Recommendation::where('user_id', $user->id)
            ->where('status', 'concluida')
            ->with(['content', 'topic'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        $performances = TopicPerformance::where('user_id', $user->id)
            ->get()
            ->keyBy('topic_id');

        return view('student.recomendacoes', compact(
            'pendentes',
            'concluidas',
            'performances'
        ));
    }

    // ─── Assistente IA ────────────────────────────────────────────

    public function assistant(Request $request)
    {
        $user = Auth::user();

        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->get();

        $prefilledRecId = $request->query('rec_id');
        $prefilledRec = null;

        if ($prefilledRecId) {
            $prefilledRec = Recommendation::where('user_id', $user->id)
                ->with(['content', 'topic'])
                ->find($prefilledRecId);
        }

        return view('student.assistant', compact(
            'recommendations',
            'prefilledRec'
        ));
    }

    // ─── Mensagem ao Assistente Gemini ────────────────────────────

    public function message(Request $request)
    {
        // Permite até 120 segundos para esta execução PHP.
        set_time_limit(120);

        $request->validate([
            'message' => 'required|string|max:2000',
            'topic_id' => 'nullable|integer',
        ]);

        $user = Auth::user();

        $message = trim($request->message);
        $topicId = $request->topic_id;

        // Contexto das recomendações pendentes
        $recsContext = Recommendation::where('user_id', $user->id)
            ->where('status', 'pendente')
            ->with(['content', 'topic'])
            ->take(5)
            ->get()
            ->map(function ($r) {
                $topicName = $r->topic->nome ?? 'Não informado';
                $contentTitle = $r->content->titulo ?? 'Não informado';
                $reason = $r->motivo ?? 'Não informado';

                return "- Tópico: {$topicName} | Conteúdo: {$contentTitle} | Motivo: {$reason}";
            })
            ->join("\n");

        // Contexto do tópico selecionado
        $topicContext = '';

        if ($topicId) {
            $topic = Topic::find($topicId);

            if ($topic) {
                $topicContext = "O estudante está perguntando especificamente sobre o tópico: {$topic->nome}.";
            }
        }

        // Instruções para o Gemini
        $systemPrompt = <<<PROMPT
Você é o Assistente Estudaí, uma IA educacional integrada à plataforma Estudaí, especializada em apoiar estudantes de tecnologia e computação.

{$topicContext}

O estudante autenticado chama-se {$user->name} e possui as seguintes recomendações de estudo pendentes geradas automaticamente pelo sistema com base em seu desempenho em quizzes:

{$recsContext}

Sua missão é:

1. Responder às perguntas do estudante de forma clara, didática e objetiva.
2. Usar exemplos de código quando relevante, formatados em blocos de código.
3. Relacionar suas respostas às recomendações pendentes quando for pertinente.
4. Usar linguagem amigável e encorajadora em português brasileiro.
5. Nunca inventar informações falsas. Se não souber algo, admita honestamente.

Responda sempre em português brasileiro.
PROMPT;

        // Chave da API
        $apiKey = env('GOOGLE_AI_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'reply' => 'Assistente IA não configurado. Verifique a variável GOOGLE_AI_KEY no arquivo .env.'
            ], 500);
        }

        // Modelo Gemini. O 3.8 Flash estava sobrecarregado no nível gratuito
        // e a resposta não voltava dentro do timeout.
        $model = 'gemini-3.5-flash-lite';

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        // Caminho do certificado SSL do PHP utilizado pelo Laravel
        $caBundle = 'C:/tools/php85/extras/ssl/cacert.pem';

        if (!is_file($caBundle)) {
            Log::error('Certificado SSL não encontrado', [
                'caminho' => $caBundle,
            ]);

            return response()->json([
                'reply' => 'Certificado SSL não encontrado. Verifique o caminho configurado no servidor.'
            ], 500);
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $message],
                    ],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens' => 1200,
                'temperature' => 0.7,
            ],
        ];

        $send = function () use ($caBundle, $apiKey, $endpoint, $payload) {
            return Http::connectTimeout(15)
                ->timeout(90)
                ->withOptions([
                    'verify' => $caBundle,
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);
        };

        try {
            $response = $send();

            if (in_array($response->status(), [429, 503], true)) {
                sleep(2);
                $response = $send();
            }

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text')
                    ?? 'Desculpe, não consegui gerar uma resposta. Tente novamente.';
            } else {
                $errorMsg = $response->json('error.message')
                    ?? 'Erro desconhecido.';

                Log::error('Erro retornado pela API Gemini', [
                    'status' => $response->status(),
                    'error' => $errorMsg,
                ]);

                $reply = "Erro ao conectar com o Gemini: {$errorMsg}";
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Falha de conexão com Gemini', [
                'message' => $e->getMessage(),
            ]);

            $reply = 'Falha de conexão com o serviço de IA. Verifique sua conexão e o certificado SSL.';

        } catch (\Throwable $e) {
            Log::error('Erro inesperado no Assistente Gemini', [
                'message' => $e->getMessage(),
            ]);

            $reply = 'Ocorreu um erro inesperado ao processar sua mensagem.';
        }

        return response()->json([
            'reply' => $reply,
        ]);
    }
}

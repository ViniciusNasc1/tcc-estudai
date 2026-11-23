<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\TopicPerformance;
use App\Models\Recommendation;
use App\Models\Content;
use App\Models\HistoricoAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show($id)
    {
        $quiz = Quiz::with(['topic', 'questions.alternatives'])->findOrFail($id);
        return view('student.quiz', compact('quiz'));
    }

    public function submit($id, Request $request)
    {
        $user = Auth::user();
        $quiz = Quiz::with('topic')->findOrFail($id);

        $request->validate([
            'answers' => 'required|array',
        ]);

        $submittedAnswers = $request->answers; // [question_id => alternative_id]

        // Carrega questões com alternativas E conteúdo vinculado
        $questions = $quiz->questions()->with(['alternatives', 'content'])->get();

        $totalQuestions  = $questions->count();
        $totalCorrect    = 0;
        $wrongContentIds = collect(); // IDs de conteúdos ligados às questões erradas

        foreach ($questions as $question) {
            $selectedAltId = $submittedAnswers[$question->id] ?? null;
            $acertou = false;

            if ($selectedAltId) {
                $alternative = $question->alternatives->firstWhere('id', $selectedAltId);
                if ($alternative && $alternative->correta) {
                    $totalCorrect++;
                    $acertou = true;
                }
            }

            // Se errou e a questão tem conteúdo específico vinculado → marca para recomendar
            if (!$acertou && $question->content_id) {
                $wrongContentIds->push($question->content_id);
            }
        }

        // Remove duplicatas (duas questões podem apontar ao mesmo conteúdo)
        $wrongContentIds = $wrongContentIds->unique()->values();

        $scorePercentage = $totalQuestions > 0
            ? ($totalCorrect / $totalQuestions) * 100
            : 0;

        // Salva resultado do quiz
        QuizResult::create([
            'user_id'              => $user->id,
            'quiz_id'              => $quiz->id,
            'pontuacao_percentual' => $scorePercentage,
            'total_questoes'       => $totalQuestions,
            'total_acertos'        => $totalCorrect,
        ]);

        // Classificação (RN06)
        if ($scorePercentage >= 80) {
            $classification = 'dominado';
            $classText      = 'Dominado';
        } elseif ($scorePercentage >= 50) {
            $classification = 'revisao_leve';
            $classText      = 'Revisão Leve';
        } else {
            $classification = 'estudo_prioritario';
            $classText      = 'Estudo Prioritário';
        }

        // Atualiza desempenho por tópico (RN05 / RN08)
        TopicPerformance::updateOrCreate(
            ['user_id'  => $user->id, 'topic_id' => $quiz->topic_id],
            ['percentual_acertos' => $scorePercentage, 'classificacao' => $classification]
        );

        // ── Recomendações adaptativas (RN07) ──────────────────────────────────
        $recommendationsGenerated = 0;

        if ($scorePercentage < 80) {

            // ── Estratégia 1: conteúdos vinculados às questões ERRADAS ──────
            $contentsToRecommend = collect();

            if ($wrongContentIds->isNotEmpty()) {
                $contentsToRecommend = Content::whereIn('id', $wrongContentIds)->get();
            }

            // ── Estratégia 2 (fallback): questões sem vínculo → tópico inteiro
            // Verifica se há questões erradas SEM content_id; se sim, pega todo
            // o conteúdo do tópico para garantir cobertura completa.
            $hasOrphanWrongs = $questions->contains(function ($q) use ($submittedAnswers) {
                $selectedAltId = $submittedAnswers[$q->id] ?? null;
                if (!$selectedAltId) return !$q->content_id; // não respondeu e sem vínculo
                $alt = $q->alternatives->firstWhere('id', $selectedAltId);
                $errou = !($alt && $alt->correta);
                return $errou && !$q->content_id;
            });

            if ($hasOrphanWrongs) {
                // Adiciona todos os conteúdos do tópico (mesclando com os específicos)
                $topicContents = Content::where('topic_id', $quiz->topic_id)->get();
                $contentsToRecommend = $contentsToRecommend
                    ->merge($topicContents)
                    ->unique('id')
                    ->values();
            }

            // ── Cria as recomendações (sem duplicatas pendentes) ─────────────
            foreach ($contentsToRecommend as $content) {
                $existing = Recommendation::where('user_id', $user->id)
                    ->where('content_id', $content->id)
                    ->where('status', 'pendente')
                    ->exists();

                if (!$existing) {
                    // Motivo descritivo: menciona se veio de questão específica ou do tópico
                    if ($wrongContentIds->contains($content->id)) {
                        $motivo = "Você errou questão(ões) do quiz \"{$quiz->titulo}\" "
                            . "relacionadas a este conteúdo específico. "
                            . "Classificação: {$classText}.";
                    } else {
                        $motivo = "Você obteve {$scorePercentage}% no quiz \"{$quiz->titulo}\" "
                            . "e errou questões sem conteúdo vinculado. Revise o material do tópico "
                            . "{$quiz->topic->nome} ({$classText}).";
                    }

                    $rec = Recommendation::create([
                        'user_id'    => $user->id,
                        'content_id' => $content->id,
                        'topic_id'   => $quiz->topic_id,
                        'motivo'     => $motivo,
                        'status'     => 'pendente',
                    ]);

                    $recommendationsGenerated++;
                    HistoricoAtividade::registrar($user->id, 'recomendacao_gerada', $rec->id);
                }
            }
        }

        // Auditoria: quiz realizado
        HistoricoAtividade::registrar($user->id, 'quiz_realizado', $quiz->id);

        $feedback = [
            'quiz_title'      => $quiz->titulo,
            'score'           => $scorePercentage,
            'total_questions' => $totalQuestions,
            'total_correct'   => $totalCorrect,
            'classification'  => $classText,
            'recs_count'      => $recommendationsGenerated,
        ];

        return redirect()->route('dashboard')->with('quiz_feedback', $feedback);
    }
}

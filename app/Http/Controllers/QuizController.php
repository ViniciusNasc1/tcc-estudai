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
        // Carrega o quiz com as questões e as alternativas correspondentes
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
        
        // Carrega todas as questões e alternativas para correção
        $questions = $quiz->questions()->with('alternatives')->get();
        $totalQuestions = $questions->count();
        $totalCorrect = 0;

        foreach ($questions as $question) {
            $selectedAltId = $submittedAnswers[$question->id] ?? null;
            if ($selectedAltId) {
                // Procura a alternativa no banco e verifica se é correta
                $alternative = $question->alternatives->firstWhere('id', $selectedAltId);
                if ($alternative && $alternative->correta) {
                    $totalCorrect++;
                }
            }
        }

        // Calcula a porcentagem
        $scorePercentage = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;

        // Salva o resultado do Quiz
        QuizResult::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'pontuacao_percentual' => $scorePercentage,
            'total_questoes' => $totalQuestions,
            'total_acertos' => $totalCorrect,
        ]);

        // Determina a classificação com base nas Regras de Negócio (RN06)
        // Acima de 80%: dominado
        // Entre 50% e 79%: revisao_leve
        // Abaixo de 50%: estudo_prioritario
        if ($scorePercentage >= 80) {
            $classification = 'dominado';
            $classText = 'Dominado';
        } elseif ($scorePercentage >= 50) {
            $classification = 'revisao_leve';
            $classText = 'Revisão Leve';
        } else {
            $classification = 'estudo_prioritario';
            $classText = 'Estudo Prioritário';
        }

        // Atualiza ou cria o desempenho por tópico (RN05 / RN08)
        TopicPerformance::updateOrCreate(
            ['user_id' => $user->id, 'topic_id' => $quiz->topic_id],
            ['percentual_acertos' => $scorePercentage, 'classificacao' => $classification]
        );

        // Mecanismo de recomendação adaptativa (RN07):
        // Recomenda automaticamente conteúdos relacionados caso o desempenho seja < 80%
        $recommendationsGenerated = 0;
        if ($scorePercentage < 80) {
            // Busca conteúdos do tópico
            $contents = Content::where('topic_id', $quiz->topic_id)->get();
            
            foreach ($contents as $content) {
                // Verifica se já existe uma recomendação pendente para este conteúdo
                $existing = Recommendation::where('user_id', $user->id)
                    ->where('content_id', $content->id)
                    ->where('status', 'pendente')
                    ->exists();

                if (!$existing) {
                    $motivo = "";
                    if ($classification === 'estudo_prioritario') {
                        $motivo = "Você acertou apenas {$scorePercentage}% das questões do quiz \"{$quiz->titulo}\". O sistema identificou dificuldades importantes em {$quiz->topic->nome} (Estudo Prioritário).";
                    } else {
                        $motivo = "Seu desempenho no quiz \"{$quiz->titulo}\" foi de {$scorePercentage}%. Recomendamos revisar este conteúdo para fixação e melhoria (Revisão Leve).";
                    }

                    $rec = Recommendation::create([
                        'user_id'    => $user->id,
                        'content_id' => $content->id,
                        'topic_id'   => $quiz->topic_id,
                        'motivo'     => $motivo,
                        'status'     => 'pendente',
                    ]);
                    $recommendationsGenerated++;

                    // ─── Auditoria: recomendação gerada ───────────────────────
                    HistoricoAtividade::registrar($user->id, 'recomendacao_gerada', $rec->id);
                }
            }
        }

        // ─── Auditoria: quiz realizado ────────────────────────────────────────
        HistoricoAtividade::registrar($user->id, 'quiz_realizado', $quiz->id);

        // Prepara mensagem de feedback para a sessão
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

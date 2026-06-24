@extends('layouts.app')

@section('title', $quiz->titulo . ' - Estudaí')

@section('content')
<div class="quiz-container">
    <!-- Header -->
    <div class="quiz-header">
        <div class="quiz-title-area">
            <a href="{{ route('home') }}" style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.4rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-arrow-left"></i> Voltar para o painel
            </a>
            <h2>{{ $quiz->titulo }}</h2>
            <span style="color: var(--text-secondary); font-size: 0.85rem;">Tópico: <strong>{{ $quiz->topic->nome }}</strong></span>
        </div>
        
        <!-- Timer -->
        <div class="quiz-timer" id="quiz-timer">
            <i class="fa-regular fa-clock"></i> <span id="timer-display">10:00</span>
        </div>
    </div>

    <!-- Hidden Form for submission -->
    <form action="{{ route('quiz.submit', $quiz->id) }}" method="POST" id="quiz-form">
        @csrf
        
        <!-- Questions Carousel managed via JS -->
        @foreach($quiz->questions as $index => $question)
            @php
                $letters = ['A', 'B', 'C', 'D', 'E'];
            @endphp
            <div class="question-wrapper" id="question-wrapper-{{ $index }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
                
                <!-- Question Card -->
                <div class="question-card">
                    <div class="question-meta">
                        <span>Questão {{ $index + 1 }} de {{ $quiz->questions->count() }}</span>
                        <span>Múltipla escolha</span>
                    </div>

                    <!-- Progress Bar inside Quiz -->
                    <div class="progress-bar-bg" style="margin-bottom: 1.5rem; height: 4px;">
                        <div class="progress-bar-fill" style="width: {{ (($index + 1) / $quiz->questions->count()) * 100 }}%;"></div>
                    </div>

                    <div class="question-text">
                        {{ $question->enunciado }}
                    </div>

                    <!-- Hidden Input to store the selected alternative -->
                    <input type="hidden" name="answers[{{ $question->id }}]" id="ans-input-{{ $question->id }}" value="">

                    <div class="alternatives-list">
                        @foreach($question->alternatives as $altIndex => $alternative)
                            <div class="alternative-item" id="alt-{{ $alternative->id }}" onclick="selectAlternative({{ $question->id }}, {{ $alternative->id }})">
                                <div class="alt-letter">{{ $letters[$altIndex] }}</div>
                                <div>{{ $alternative->descricao }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="quiz-footer-actions">
                    <div>
                        @if($index > 0)
                            <button type="button" class="btn" style="background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);" onclick="showQuestion({{ $index - 1 }})">
                                <i class="fa-solid fa-chevron-left"></i> Anterior
                            </button>
                        @endif
                    </div>
                    
                    <div>
                        @if($index < $quiz->questions->count() - 1)
                            <button type="button" class="btn btn-primary" onclick="showQuestion({{ $index + 1 }})">
                                Próxima <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        @else
                            <button type="button" class="btn" style="background-color: var(--success); color: #fff;" onclick="submitQuiz()">
                                <i class="fa-solid fa-check"></i> Finalizar Quiz
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        @endforeach
    </form>
</div>
@endsection

@section('scripts')
<script>
    let currentQuestionIdx = 0;
    const totalQuestions = {{ $quiz->questions->count() }};
    
    // Timer Variables
    let timeRemaining = 10 * 60; // 10 minutes in seconds
    const timerDisplay = document.getElementById('timer-display');
    const timerContainer = document.getElementById('quiz-timer');
    
    // Start countdown
    const timerInterval = setInterval(() => {
        timeRemaining--;
        
        let minutes = Math.floor(timeRemaining / 60);
        let seconds = timeRemaining % 60;
        
        // Pad zero
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        
        timerDisplay.textContent = `${minutes}:${seconds}`;
        
        // Visual warning if under 1 minute
        if (timeRemaining < 60) {
            timerContainer.classList.add('critical');
        }
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('O tempo limite do quiz acabou! Suas respostas serão enviadas automaticamente.');
            submitQuiz();
        }
    }, 1000);

    function showQuestion(index) {
        // Hide current
        document.getElementById('question-wrapper-' + currentQuestionIdx).style.display = 'none';
        
        // Show target
        document.getElementById('question-wrapper-' + index).style.display = 'block';
        
        currentQuestionIdx = index;
    }

    function selectAlternative(questionId, alternativeId) {
        // Find input
        const input = document.getElementById('ans-input-' + questionId);
        
        // Reset current selections for this question's alternatives
        const questionWrapper = document.getElementById('question-wrapper-' + currentQuestionIdx);
        const altItems = questionWrapper.getElementsByClassName('alternative-item');
        for (let item of altItems) {
            item.classList.remove('selected');
        }
        
        // Select clicked alternative
        const clickedItem = document.getElementById('alt-' + alternativeId);
        clickedItem.classList.add('selected');
        
        // Update value
        input.value = alternativeId;
    }

    function submitQuiz() {
        // Verify if all questions have been answered
        const inputs = document.querySelectorAll('input[type="hidden"][name^="answers"]');
        let answeredCount = 0;
        inputs.forEach(input => {
            if (input.value !== "") {
                answeredCount++;
            }
        });

        if (answeredCount < totalQuestions) {
            const confirmSubmit = confirm(`Você respondeu apenas ${answeredCount} de ${totalQuestions} questões. Tem certeza que deseja finalizar?`);
            if (!confirmSubmit) return;
        }

        // Stop timer and submit
        clearInterval(timerInterval);
        document.getElementById('quiz-form').submit();
    }
</script>
@endsection

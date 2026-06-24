@extends('layouts.app')

@section('title', 'Meu Dashboard - Estudaí')

@section('content')
<!-- Feedback do Quiz Finalizado -->
@if(session('quiz_feedback'))
    @php
        $fb = session('quiz_feedback');
        $alertColor = 'var(--success)';
        $alertLight = 'var(--success-light)';
        $alertIcon = 'fa-circle-check';
        
        if ($fb['score'] < 50) {
            $alertColor = 'var(--danger)';
            $alertLight = 'var(--danger-light)';
            $alertIcon = 'fa-triangle-exclamation';
        } elseif ($fb['score'] < 80) {
            $alertColor = 'var(--warning)';
            $alertLight = 'var(--warning-light)';
            $alertIcon = 'fa-circle-exclamation';
        }
    @endphp
    <div style="background-color: {{ $alertLight }}; border: 2px solid {{ $alertColor }}; border-radius: var(--border-radius); padding: 1.8rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1.5rem;">
        <div style="font-size: 2.2rem; color: {{ $alertColor }};">
            <i class="fa-solid {{ $alertIcon }}"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="color: var(--text-primary); font-weight: 700; margin-bottom: 0.5rem; font-size: 1.3rem;">Quiz Finalizado com Sucesso!</h3>
            <p style="margin-bottom: 1rem; font-size: 0.95rem; color: var(--text-secondary);">
                Você concluiu o teste <strong>{{ $fb['quiz_title'] }}</strong>. O algoritmo analisou seu desempenho e atualizou seu histórico.
            </p>
            
            <div class="grid-4" style="gap: 1rem; margin-bottom: 1rem;">
                <div class="card" style="padding: 0.8rem; background-color: rgba(255,255,255,0.03); border-color: var(--border-color); text-align: center;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Sua Nota</span>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-top: 0.2rem; color: {{ $alertColor }};">{{ round($fb['score']) }}%</h4>
                </div>
                <div class="card" style="padding: 0.8rem; background-color: rgba(255,255,255,0.03); border-color: var(--border-color); text-align: center;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Acertos</span>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-top: 0.2rem;">{{ $fb['total_correct'] }} / {{ $fb['total_questions'] }}</h4>
                </div>
                <div class="card" style="padding: 0.8rem; background-color: rgba(255,255,255,0.03); border-color: var(--border-color); text-align: center;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Status</span>
                    <h4 style="font-size: 1.15rem; font-weight: 700; margin-top: 0.3rem;">{{ $fb['classification'] }}</h4>
                </div>
                <div class="card" style="padding: 0.8rem; background-color: rgba(255,255,255,0.03); border-color: var(--border-color); text-align: center;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">IA Recomenda</span>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-top: 0.2rem; color: var(--warning);">{{ $fb['recs_count'] }} itens</h4>
                </div>
            </div>
            
            @if($fb['recs_count'] > 0)
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-robot" style="color: var(--primary);"></i> <strong>Dica da Inteligência Artificial:</strong> O sistema gerou {{ $fb['recs_count'] }} novos conteúdos na sua página inicial para te ajudar a estudar os conceitos que você errou neste quiz. Acesse a aba <strong>Assistente</strong> para discuti-los com a IA!
                </div>
            @else
                <div style="font-size: 0.85rem; color: var(--success);">
                    <i class="fa-solid fa-square-check"></i> <strong>Parabéns!</strong> Você atingiu domínio neste tópico. Nenhuma recomendação de revisão foi gerada para esta tentativa.
                </div>
            @endif
        </div>
    </div>
@endif

<!-- Metric Cards Row -->
<div class="grid-4" style="margin-bottom: 2rem;">
    <!-- Metric 1 -->
    <div class="card stat-card">
        <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
            <i class="fa-solid fa-list-check"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $quizzesRealizados }}</span>
            <span class="stat-label">Quizzes realizados</span>
        </div>
    </div>
    <!-- Metric 2 -->
    <div class="card stat-card">
        <div class="stat-icon" style="background-color: var(--success-light); color: var(--success);">
            <i class="fa-solid fa-bullseye"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ round($mediaGeral) }}%</span>
            <span class="stat-label">Média geral de acertos</span>
        </div>
    </div>
    <!-- Metric 3 -->
    <div class="card stat-card">
        <div class="stat-icon" style="background-color: rgba(168, 85, 247, 0.15); color: #a855f7;">
            <i class="fa-solid fa-book-open-reader"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $conteudosVistos }}</span>
            <span class="stat-label">Conteúdos vistos</span>
        </div>
    </div>
    <!-- Metric 4 -->
    <div class="card stat-card">
        <div class="stat-icon" style="background-color: var(--warning-light); color: var(--warning);">
            <i class="fa-solid fa-lightbulb"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $recomendacoesPendentes }}</span>
            <span class="stat-label">Recomendações ativas</span>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid-2" style="margin-bottom: 2rem;">
    
    <!-- Chart 1: Performance by Topic -->
    <div class="card">
        <h4 style="margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-chart-bar" style="color: var(--primary);"></i> Desempenho por Tópico
        </h4>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="topicChart"></canvas>
        </div>
    </div>
    
    <!-- Chart 2: Quizzes Evolution -->
    <div class="card">
        <h4 style="margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-chart-line" style="color: #a855f7;"></i> Evolução dos Quizzes
        </h4>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="evolutionChart"></canvas>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Gráfico de Barras: Desempenho por Tópico
    const ctxTopic = document.getElementById('topicChart').getContext('2d');
    
    // Dados injetados pelo backend
    const topicNames = {!! json_encode($topicPerformances->pluck('topic.nome')) !!};
    const topicScores = {!! json_encode($topicPerformances->pluck('percentual_acertos')) !!};
    
    // Cria cores dinâmicas baseadas na nota (verde >= 80, amarelo >= 50, vermelho < 50)
    const backgroundColors = topicScores.map(score => {
        if (score >= 80) return 'rgba(48, 209, 88, 0.75)'; // success color
        if (score >= 50) return 'rgba(255, 214, 10, 0.75)'; // warning color
        return 'rgba(255, 69, 58, 0.75)'; // danger color
    });

    const borderColors = topicScores.map(score => {
        if (score >= 80) return 'rgb(48, 209, 88)';
        if (score >= 50) return 'rgb(255, 214, 10)';
        return 'rgb(255, 69, 58)';
    });

    new Chart(ctxTopic, {
        type: 'bar',
        data: {
            labels: topicNames,
            datasets: [{
                label: 'Desempenho (%)',
                data: topicScores,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1.5,
                borderRadius: 8,
                barThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Nota: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#98a0b3',
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#98a0b3',
                        font: {
                            family: 'Outfit',
                            weight: '600'
                        }
                    }
                }
            }
        }
    });

    // 2. Gráfico de Linha: Evolução dos Quizzes
    const ctxEvolution = document.getElementById('evolutionChart').getContext('2d');
    
    // Dados injetados pelo backend
    const evolutionResults = {!! json_encode($evolutionResults) !!};
    
    const lineLabels = evolutionResults.map((res, index) => {
        // Se tiver mais de 5 resultados, mostramos apenas o índice simplificado
        return `Tentativa ${index + 1}`;
    });
    const lineScores = evolutionResults.map(res => res.pontuacao_percentual);

    new Chart(ctxEvolution, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: 'Sua Nota',
                data: lineScores,
                borderColor: '#5e5ce6',
                backgroundColor: 'rgba(94, 92, 230, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#a855f7',
                pointBorderColor: '#fff',
                pointBorderWidth: 1.5,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#98a0b3',
                        font: {
                            family: 'Outfit'
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#98a0b3',
                        font: {
                            family: 'Outfit'
                        }
                    }
                }
            }
        }
    });
</script>
@endsection

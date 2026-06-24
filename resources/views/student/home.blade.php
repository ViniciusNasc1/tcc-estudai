@extends('layouts.app')

@section('title', 'Painel de Estudos - Estudaí')

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-info">
        <h2>Olá, {{ $user->name }}! <i class="fa-solid fa-hand-wave" style="color: var(--warning);"></i></h2>
        @if($recommendations->count() > 0)
            <p>Você tem <strong>{{ $recommendations->count() }} recomendações pendentes</strong> de estudo baseadas em seu desempenho.</p>
        @else
            <p>Parabéns! Você está em dia com seus estudos e não possui nenhuma recomendação pendente.</p>
        @endif
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="btn" style="background-color: #fff; color: var(--primary);">
            Ver dashboard <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Grid 2-columns for Topicos and Quizzes -->
<div style="display: flex; flex-direction: column; gap: 2.5rem;">
    
    <!-- Tópicos Disponíveis Section -->
    <div>
        <h3 class="section-title">
            <i class="fa-solid fa-book-bookmark" style="color: var(--primary);"></i> Tópicos de Estudo
        </h3>
        
        <div class="grid-4">
            @foreach($topics as $topic)
                @php
                    $perf = $performances->get($topic->id);
                    $score = $perf ? $perf->percentual_acertos : 0;
                    
                    // Definir o ícone baseado no nome do tópico
                    $iconClass = 'fa-database';
                    if (stripos($topic->nome, 'algoritmo') !== false) {
                        $iconClass = 'fa-code';
                    } elseif (stripos($topic->nome, 'rede') !== false) {
                        $iconClass = 'fa-network-wired';
                    } elseif (stripos($topic->nome, 'web') !== false) {
                        $iconClass = 'fa-globe';
                    }
                @endphp
                <div class="card topic-card" onclick="window.location='{{ route('quiz.show', $topic->quizzes->first()->id ?? 1) }}'">
                    <div class="topic-header">
                        <div class="topic-icon">
                            <i class="fa-solid {{ $iconClass }}"></i>
                        </div>
                        <div>
                            <h3>{{ $topic->nome }}</h3>
                            <span class="topic-meta">
                                {{ $topic->contents->count() }} conteúdos • {{ $topic->quizzes->count() }} quiz
                            </span>
                        </div>
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress-labels">
                            <span>Desempenho</span>
                            <strong>{{ round($score) }}%</strong>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $score }}%;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recommendations and Latest Quizzes row -->
    <div class="grid-2">
        
        <!-- Recomendações de Estudo -->
        <div>
            <h3 class="section-title">
                <i class="fa-solid fa-lightbulb" style="color: var(--warning);"></i> Recomendações Pendentes
            </h3>
            
            <div class="list-wrapper">
                @if($recommendations->count() > 0)
                    @foreach($recommendations as $rec)
                        @php
                            $isPriority = $rec->content->topic_id === 1 || $rec->content->topic_id === 4; 
                            // Banco de dados e Prog Web tem scores baixos (45% e 20% mockados), logo são prioritários
                            if ($rec->topic->nome == 'Banco de Dados' || $rec->topic->nome == 'Prog. Web') {
                                $isPriority = true;
                            } else {
                                $isPriority = false;
                            }
                        @endphp
                        <div class="rec-item">
                            <div class="rec-content">
                                @if($isPriority)
                                    <span class="rec-badge badge-prioritario">Prioritário</span>
                                @else
                                    <span class="rec-badge badge-revisao">Revisão Leve</span>
                                @endif
                                <div class="rec-info">
                                    <h4>{{ $rec->content->titulo }}</h4>
                                    <p style="margin-bottom: 0.4rem;">Tópico: <strong>{{ $rec->topic->nome }}</strong></p>
                                    <p style="font-size: 0.8rem; opacity: 0.85;">{{ $rec->motivo }}</p>
                                </div>
                            </div>
                            
                            <div class="rec-actions">
                                <a href="{{ $rec->content->url }}" target="_blank" class="btn" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); font-size: 0.8rem; padding: 0.5rem 0.8rem;" onclick="event.stopPropagation(); alert('Simulação: Abrindo conteúdo de estudo em nova aba.')">
                                    <i class="fa-solid fa-book-open"></i> Ver
                                </a>
                                <a href="{{ route('assistant', ['rec_id' => $rec->id]) }}" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.5rem 0.8rem;" onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-robot"></i> IA
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        <i class="fa-solid fa-circle-check" style="font-size: 2rem; color: var(--success); margin-bottom: 0.8rem;"></i>
                        <p>Nenhuma recomendação pendente. Continue mantendo notas altas nos quizzes!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Últimos Quizzes Realizados -->
        <div>
            <h3 class="section-title">
                <i class="fa-solid fa-history" style="color: var(--primary);"></i> Histórico de Quizzes
            </h3>
            
            <div class="list-wrapper">
                @if($latestResults->count() > 0)
                    @foreach($latestResults as $result)
                        @php
                            $score = $result->pontuacao_percentual;
                            $badgeClass = 'badge-dominado';
                            if ($score < 50) {
                                $badgeClass = 'badge-prioritario';
                            } elseif ($score < 80) {
                                $badgeClass = 'badge-revisao';
                            }
                        @endphp
                        <div class="rec-item" style="cursor: default;">
                            <div class="rec-content" style="align-items: center;">
                                <div class="topic-icon" style="width: 36px; height: 36px; font-size: 1rem; flex-shrink: 0;">
                                    <i class="fa-solid fa-circle-question"></i>
                                </div>
                                <div class="rec-info">
                                    <h4 style="font-size: 0.95rem;">{{ $result->quiz->titulo }}</h4>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary);">
                                        Realizado {{ $result->created_at->diffForHumans() }} • {{ $result->total_acertos }}/{{ $result->total_questoes }} acertos
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <span class="rec-badge {{ $badgeClass }}" style="font-size: 0.8rem; padding: 0.4rem 0.7rem;">
                                    {{ round($score) }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        <i class="fa-solid fa-hourglass" style="font-size: 2rem; margin-bottom: 0.8rem;"></i>
                        <p>Você ainda não realizou nenhum quiz. Escolha um tópico acima para começar!</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

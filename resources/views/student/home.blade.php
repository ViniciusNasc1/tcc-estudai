@extends('layouts.app')

@section('title', 'Início — Estudaí')

@section('content')

{{-- ─── Banner de boas-vindas ──────────────────────────────────────────────── --}}
<div class="welcome-banner" style="margin-bottom:2rem;">
    <div class="welcome-info">
        <h2 style="display:flex; align-items:center; gap:0.6rem;">
            Olá, {{ explode(' ', $user->name)[0] }}! <i class="fa-solid fa-hand-wave" style="color:var(--warning);"></i>
        </h2>
        @if($recommendations->count() > 0)
            <p>Você tem <strong>{{ $recommendations->count() }} recomendação(ões) pendente(s)</strong> baseadas no seu desempenho nos quizzes.</p>
        @else
            <p>Parabéns! Você está em dia com seus estudos. Continue assim! 🎉</p>
        @endif
    </div>
    <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
        <a href="{{ route('topicos') }}" class="btn" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff;">
            <i class="fa-solid fa-book-open"></i> Ver Tópicos
        </a>
        <a href="{{ route('dashboard') }}" class="btn" style="background:#fff; color:var(--primary); font-weight:700;">
            <i class="fa-solid fa-chart-simple"></i> Dashboard
        </a>
    </div>
</div>

{{-- ─── Cards de estatísticas rápidas ──────────────────────────────────────── --}}
@php
    $totalQuizzesFeitos = $latestResults->count();
    $mediaGeral = $latestResults->count() > 0 ? round($latestResults->avg('pontuacao_percentual')) : 0;
    $topicosEstudados = $performances->count();
@endphp
<div class="grid-4" style="margin-bottom:2.5rem;">
    <div class="card stat-card">
        <div class="stat-icon" style="background:var(--primary-light); color:var(--primary);">
            <i class="fa-solid fa-clipboard-check"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalQuizzesFeitos }}</span>
            <span class="stat-label">Quizzes realizados</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,0.15); color:#a855f7;">
            <i class="fa-solid fa-bullseye"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $mediaGeral }}%</span>
            <span class="stat-label">Média geral</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background:var(--success-light); color:var(--success);">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $topicosEstudados }}</span>
            <span class="stat-label">Tópicos estudados</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background:var(--warning-light); color:var(--warning);">
            <i class="fa-solid fa-lightbulb"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $recommendations->count() }}</span>
            <span class="stat-label">Recomendações pendentes</span>
        </div>
    </div>
</div>

{{-- ─── Tópicos de Estudo (cards clicáveis → página do tópico) ─────────────── --}}
<div style="margin-bottom:2.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
        <h3 class="section-title" style="margin:0;">
            <i class="fa-solid fa-book-bookmark" style="color:var(--primary);"></i> Tópicos de Estudo
        </h3>
        <a href="{{ route('topicos') }}" style="font-size:0.85rem; color:var(--primary); text-decoration:none; font-weight:600;">
            Ver todos <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="grid-4">
        @forelse($topics->take(8) as $topic)
            @php
                $perf   = $performances->get($topic->id);
                $score  = $perf ? round($perf->percentual_acertos) : null;
                $status = $perf ? $perf->classificacao : null;
                $badgeClass = match($status) {
                    'dominado'           => 'badge-dominado',
                    'revisao_leve'       => 'badge-revisao',
                    'estudo_prioritario' => 'badge-prioritario',
                    default              => '',
                };
                $badgeLabel = match($status) {
                    'dominado'           => 'Dominado',
                    'revisao_leve'       => 'Revisão',
                    'estudo_prioritario' => 'Prioritário',
                    default              => 'Novo',
                };
                $iconClass = match(true) {
                    str_contains(strtolower($topic->nome), 'banco')  => 'fa-database',
                    str_contains(strtolower($topic->nome), 'rede')   => 'fa-network-wired',
                    str_contains(strtolower($topic->nome), 'progra') => 'fa-code',
                    str_contains(strtolower($topic->nome), 'web')    => 'fa-globe',
                    str_contains(strtolower($topic->nome), 'sist')   => 'fa-server',
                    str_contains(strtolower($topic->nome), 'algor')  => 'fa-diagram-project',
                    default => 'fa-book',
                };
                $barColor = $score >= 80 ? 'var(--success)' : ($score >= 50 ? 'var(--warning)' : 'var(--danger)');
            @endphp
            <a href="{{ route('topico.show', $topic) }}" style="text-decoration:none;" class="topic-card-link">
                <div class="card topic-card" style="cursor:pointer; transition:transform 0.2s, box-shadow 0.2s; height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.8rem;">
                        <div class="topic-icon"><i class="fa-solid {{ $iconClass }}"></i></div>
                        @if($status)
                            <span class="rec-badge {{ $badgeClass }}" style="font-size:0.7rem;">{{ $badgeLabel }}</span>
                        @else
                            <span style="font-size:0.7rem; color:var(--text-muted); background:var(--bg-primary); border:1px solid var(--border-color); border-radius:20px; padding:0.2rem 0.6rem; font-weight:600;">Novo</span>
                        @endif
                    </div>
                    <h3 style="font-size:0.95rem; font-weight:700; margin-bottom:0.4rem; color:var(--text-primary);">{{ $topic->nome }}</h3>
                    <p style="font-size:0.78rem; color:var(--text-secondary); margin-bottom:0.8rem;">
                        {{ $topic->contents->count() }} conteúdo(s) • {{ $topic->quizzes->count() }} quiz(zes)
                    </p>
                    @if($score !== null)
                        <div>
                            <div class="progress-labels" style="font-size:0.75rem;">
                                <span style="color:var(--text-secondary);">Desempenho</span>
                                <strong style="color:{{ $barColor }};">{{ $score }}%</strong>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width:{{ $score }}%; background:{{ $barColor }};"></div>
                            </div>
                        </div>
                    @else
                        <div style="font-size:0.78rem; color:var(--text-muted); display:flex; align-items:center; gap:0.4rem; margin-top:auto;">
                            <i class="fa-solid fa-play" style="color:var(--primary);"></i> Clique para começar
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="card" style="grid-column:1/-1; text-align:center; padding:2rem; color:var(--text-secondary);">
                Nenhum tópico disponível ainda.
            </div>
        @endforelse
    </div>
</div>

{{-- ─── Grid: Recomendações + Histórico ───────────────────────────────────── --}}
<div class="grid-2">

    {{-- Recomendações pendentes --}}
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
            <h3 class="section-title" style="margin:0;">
                <i class="fa-solid fa-lightbulb" style="color:var(--warning);"></i> Recomendações
            </h3>
            <a href="{{ route('recomendacoes') }}" style="font-size:0.85rem; color:var(--primary); text-decoration:none; font-weight:600;">
                Ver todas <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div style="display:flex; flex-direction:column; gap:0.8rem;">
            @forelse($recommendations->take(4) as $rec)
                @php
                    $perf = $performances->get($rec->topic_id);
                    $s    = $perf ? $perf->classificacao : 'estudo_prioritario';
                    $bc   = $s === 'dominado' ? 'badge-dominado' : ($s === 'revisao_leve' ? 'badge-revisao' : 'badge-prioritario');
                    $bl   = $s === 'dominado' ? 'Dominado' : ($s === 'revisao_leve' ? 'Revisão Leve' : 'Prioritário');
                @endphp
                <div class="card" style="padding:1rem; display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem;">
                            <span class="rec-badge {{ $bc }}" style="font-size:0.7rem; flex-shrink:0;">{{ $bl }}</span>
                            <span style="font-size:0.75rem; color:var(--text-secondary); font-weight:600;">{{ $rec->topic->nome }}</span>
                        </div>
                        <h4 style="font-size:0.9rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $rec->content->titulo }}</h4>
                    </div>
                    <div style="display:flex; gap:0.4rem; flex-shrink:0;">
                        @if($rec->content->url)
                            <a href="{{ $rec->content->url }}" target="_blank" title="Ver conteúdo" style="background:var(--bg-primary); border:1px solid var(--border-color); color:var(--text-primary); border-radius:8px; padding:0.4rem 0.6rem; font-size:0.8rem; text-decoration:none;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        @endif
                        <a href="{{ route('assistant', ['rec_id' => $rec->id]) }}" title="Perguntar ao assistente IA" style="background:var(--primary-light); border:none; color:var(--primary); border-radius:8px; padding:0.4rem 0.6rem; font-size:0.8rem; text-decoration:none; display:flex; align-items:center;">
                            <i class="fa-solid fa-robot"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="card" style="text-align:center; padding:2rem; color:var(--text-secondary);">
                    <i class="fa-solid fa-circle-check" style="font-size:2rem; color:var(--success); display:block; margin-bottom:0.8rem;"></i>
                    <p>Nenhuma recomendação pendente.<br>Continue mantendo notas altas!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Histórico de quizzes --}}
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
            <h3 class="section-title" style="margin:0;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);"></i> Histórico de Quizzes
            </h3>
            <a href="{{ route('historico.quizzes') }}" style="font-size:0.85rem; color:var(--primary); text-decoration:none; font-weight:600;">
                Ver todos <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div style="display:flex; flex-direction:column; gap:0.8rem;">
            @forelse($latestResults as $result)
                @php
                    $sc = round($result->pontuacao_percentual);
                    $bc = $sc >= 80 ? 'badge-dominado' : ($sc >= 50 ? 'badge-revisao' : 'badge-prioritario');
                @endphp
                <div class="card" style="padding:1rem; display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:0.8rem; flex:1; min-width:0;">
                        <div style="width:36px; height:36px; border-radius:8px; background:var(--primary-light); display:flex; align-items:center; justify-content:center; color:var(--primary); flex-shrink:0;">
                            <i class="fa-solid fa-circle-question"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:600; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $result->quiz->titulo }}</div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);">
                                {{ $result->created_at->diffForHumans() }} • {{ $result->total_acertos }}/{{ $result->total_questoes }} acertos
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.6rem; flex-shrink:0;">
                        <span class="rec-badge {{ $bc }}">{{ $sc }}%</span>
                        <a href="{{ route('quiz.show', $result->quiz_id) }}" title="Refazer quiz" style="background:var(--bg-primary); border:1px solid var(--border-color); border-radius:8px; padding:0.3rem 0.6rem; font-size:0.75rem; color:var(--text-secondary); text-decoration:none;">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="card" style="text-align:center; padding:2rem; color:var(--text-secondary);">
                    <i class="fa-solid fa-hourglass" style="font-size:2rem; display:block; margin-bottom:0.8rem; opacity:0.5;"></i>
                    <p>Nenhum quiz realizado ainda.<br>Escolha um tópico acima para começar!</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
.topic-card-link:hover .topic-card {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(94,92,230,0.18);
    border-color: var(--primary);
}
</style>
@endsection

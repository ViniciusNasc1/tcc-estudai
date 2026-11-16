@extends('layouts.app')

@section('title', 'Tópicos de Estudo — Estudaí')

@section('content')
{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-info">
        <h2 style="display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-book-open" style="font-size:1.3rem;"></i>
            Tópicos de Estudo
        </h2>
        <p>Explore todos os tópicos disponíveis e acesse os conteúdos recomendados para você.</p>
    </div>
</div>

{{-- Grid de tópicos --}}
<div class="grid-3">
    @forelse($topics as $topic)
        @php
            $perf    = $performances->get($topic->id);
            $pct     = $perf ? round($perf->percentual_acertos) : null;
            $status  = $perf ? $perf->classificacao : null;
            $badgeLabel = match($status) {
                'dominado'          => 'Dominado',
                'revisao_leve'      => 'Revisão leve',
                'estudo_prioritario'=> 'Prioritário',
                default             => 'Não iniciado',
            };
            $badgeClass = match($status) {
                'dominado'          => 'badge-dominado',
                'revisao_leve'      => 'badge-revisao',
                'estudo_prioritario'=> 'badge-prioritario',
                default             => '',
            };
            $topicIcon = match(true) {
                str_contains(strtolower($topic->nome), 'banco')   => 'fa-database',
                str_contains(strtolower($topic->nome), 'redes')   => 'fa-network-wired',
                str_contains(strtolower($topic->nome), 'progra')  => 'fa-code',
                str_contains(strtolower($topic->nome), 'web')     => 'fa-globe',
                str_contains(strtolower($topic->nome), 'sist')    => 'fa-server',
                default => 'fa-book',
            };
        @endphp
        <div class="card" style="display:flex; flex-direction:column; gap:1rem;">
            {{-- Cabeçalho do card --}}
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:0.8rem;">
                    <div class="topic-icon"><i class="fa-solid {{ $topicIcon }}"></i></div>
                    <h3 style="font-size:1rem; font-weight:700; line-height:1.2;">{{ $topic->nome }}</h3>
                </div>
                @if($status)
                    <span class="rec-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                @endif
            </div>

            {{-- Descrição --}}
            @if($topic->descricao)
                <p style="font-size:0.85rem; color:var(--text-secondary); flex:1;">{{ Str::limit($topic->descricao, 100) }}</p>
            @endif

            {{-- Barra de progresso se houver desempenho --}}
            @if($pct !== null)
                <div>
                    <div class="progress-labels">
                        <span style="font-size:0.8rem; color:var(--text-secondary);">Meu desempenho</span>
                        <strong style="font-size:0.8rem; color:{{ $pct >= 80 ? 'var(--success)' : ($pct >= 50 ? 'var(--warning)' : 'var(--danger)') }};">{{ $pct }}%</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{ $pct }}%; background:{{ $pct >= 80 ? 'var(--success)' : ($pct >= 50 ? 'var(--warning)' : 'var(--danger)') }};"></div>
                    </div>
                </div>
            @endif

            {{-- Metadados --}}
            <div style="display:flex; gap:1rem; font-size:0.8rem; color:var(--text-secondary);">
                <span><i class="fa-solid fa-file-lines"></i> {{ $topic->contents->count() }} conteúdo(s)</span>
                <span><i class="fa-solid fa-circle-question"></i> {{ $topic->quizzes->count() }} quiz(zes)</span>
            </div>

            {{-- Conteúdos disponíveis --}}
            @if($topic->contents->count() > 0)
                <div style="border-top:1px solid var(--border-color); padding-top:0.8rem; display:flex; flex-direction:column; gap:0.4rem;">
                    <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600; margin-bottom:0.2rem;">Materiais</span>
                    @foreach($topic->contents->take(3) as $content)
                        @if($content->url)
                            <a href="{{ $content->url }}" target="_blank" style="font-size:0.85rem; color:var(--primary); display:flex; align-items:center; gap:0.4rem; text-decoration:none;">
                                <i class="fa-solid fa-external-link" style="font-size:0.7rem;"></i>
                                {{ Str::limit($content->titulo, 45) }}
                            </a>
                        @else
                            <span style="font-size:0.85rem; color:var(--text-primary); display:flex; align-items:center; gap:0.4rem;">
                                <i class="fa-solid fa-file" style="font-size:0.7rem; color:var(--text-secondary);"></i>
                                {{ Str::limit($content->titulo, 45) }}
                            </span>
                        @endif
                    @endforeach
                    @if($topic->contents->count() > 3)
                        <span style="font-size:0.8rem; color:var(--text-muted);">+{{ $topic->contents->count() - 3 }} mais...</span>
                    @endif
                </div>
            @endif

            {{-- Botões de ação --}}
            <div style="display:flex; gap:0.6rem; margin-top:auto;">
                @foreach($topic->quizzes->take(1) as $quiz)
                    <a href="{{ route('quiz.show', $quiz) }}" class="btn btn-primary" style="flex:1; text-align:center; font-size:0.85rem; padding:0.6rem;">
                        <i class="fa-solid fa-play"></i> Fazer Quiz
                    </a>
                @endforeach
                <a href="{{ route('assistant', ['topic_id' => $topic->id]) }}" class="btn" style="flex:1; text-align:center; font-size:0.85rem; padding:0.6rem; background:var(--primary-light); color:var(--primary); border:none;">
                    <i class="fa-solid fa-robot"></i> Perguntar IA
                </a>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-secondary);">
            <i class="fa-solid fa-book-open" style="font-size:2.5rem; display:block; margin-bottom:1rem; opacity:0.4;"></i>
            Nenhum tópico disponível no momento.
        </div>
    @endforelse
</div>
@endsection

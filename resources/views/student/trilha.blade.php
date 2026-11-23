@extends('layouts.app')

@section('title', 'Minha Trilha de Estudos - Estudaí')

@section('styles')
<style>
    /* ── Trilha page layout ── */
    .trilha-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    .trilha-header .trilha-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        flex-shrink: 0;
    }
    .trilha-subtitle {
        color: var(--text-secondary);
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    /* ── Roadmap timeline ── */
    .trilha-timeline {
        position: relative;
        padding-left: 60px;
    }
    /* Vertical line running through all steps */
    .trilha-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--border-color);
        border-radius: 3px;
    }

    .trilha-step {
        position: relative;
        margin-bottom: 1.5rem;
    }
    /* Numbered circle */
    .trilha-step-num {
        position: absolute;
        left: -48px;
        top: 18px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 3px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-secondary);
        z-index: 1;
        transition: border-color 0.2s, color 0.2s;
    }
    .trilha-step.nivel-prioritario .trilha-step-num {
        border-color: var(--danger);
        color: var(--danger);
    }
    .trilha-step.nivel-revisao .trilha-step-num {
        border-color: var(--warning);
        color: var(--warning);
    }
    .trilha-step.nivel-explorar .trilha-step-num {
        border-color: #6366f1;
        color: #6366f1;
    }

    /* ── Step card ── */
    .trilha-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .trilha-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-1px);
    }
    .trilha-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }
    .trilha-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    /* ── Badges ── */
    .badge-nivel {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-nivel.prioritario {
        background: rgba(239,68,68,0.15);
        color: var(--danger);
        border: 1px solid rgba(239,68,68,0.3);
    }
    .badge-nivel.revisao {
        background: rgba(234,179,8,0.15);
        color: var(--warning);
        border: 1px solid rgba(234,179,8,0.3);
    }
    .badge-nivel.explorar {
        background: rgba(99,102,241,0.12);
        color: #6366f1;
        border: 1px solid rgba(99,102,241,0.3);
    }

    /* ── Meta info row ── */
    .trilha-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.82rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }
    .trilha-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .trilha-meta .perf-acertos {
        font-weight: 600;
    }
    .trilha-meta .perf-acertos.baixo  { color: var(--danger); }
    .trilha-meta .perf-acertos.medio  { color: var(--warning); }
    .trilha-meta .perf-acertos.alto   { color: var(--success); }

    /* ── Buttons row ── */
    .trilha-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .trilha-actions .btn {
        font-size: 0.82rem;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn-outline-secondary {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .btn-outline-secondary:hover {
        background: var(--border-color);
        color: var(--text-primary);
    }

    /* ── Empty / congrats card ── */
    .trilha-empty {
        text-align: center;
        padding: 3rem 2rem;
    }
    .trilha-empty .empty-icon {
        font-size: 3.5rem;
        color: var(--success);
        margin-bottom: 1rem;
    }
    .trilha-empty h2 {
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    .trilha-empty p {
        color: var(--text-secondary);
        max-width: 480px;
        margin: 0 auto;
    }

    /* ── Rec badge ── */
    .rec-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(99,102,241,0.1);
        color: #6366f1;
        border-radius: 50px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(99,102,241,0.25);
    }
</style>
@endsection

@section('content')
<div class="container" style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">

    {{-- ── Header ── --}}
    <div class="trilha-header">
        <div class="trilha-icon">
            <i class="fa-solid fa-route"></i>
        </div>
        <div>
            <h1 class="section-title" style="margin: 0;">Minha Trilha de Estudos</h1>
        </div>
    </div>
    <p class="trilha-subtitle">
        Sua trilha é gerada automaticamente com base no seu desempenho nos quizzes.
        Os tópicos que precisam de mais atenção aparecem primeiro.
    </p>

    {{-- ── Empty state (all mastered or no topics) ── --}}
    @if($trilha->isEmpty())
        <div class="card trilha-empty">
            <div class="empty-icon"><i class="fa-solid fa-trophy"></i></div>
            <h2>Parabéns! 🎉</h2>
            <p>
                Você dominou todos os tópicos disponíveis na plataforma.
                Fique de olho em novos conteúdos que serão adicionados em breve!
            </p>
            <a href="{{ route('topicos') }}" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-book-open"></i> Ver Tópicos
            </a>
        </div>
    @else
        {{-- ── Roadmap ── --}}
        <div class="trilha-timeline">
            @foreach($trilha as $index => $item)
                @php
                    $topic  = $item['topic'];
                    $nivel  = $item['nivel'];
                    $perf   = $item['perf'];
                    $recs   = $recsCount->get($topic->id, 0);
                    $pct    = $perf ? round($perf->percentual_acertos ?? 0) : null;
                    $pctClass = $pct === null ? '' : ($pct < 40 ? 'baixo' : ($pct < 70 ? 'medio' : 'alto'));

                    $badgeLabel = match($nivel) {
                        'prioritario' => 'Prioritário',
                        'revisao'     => 'Revisão',
                        default       => 'Explorar',
                    };
                    $badgeIcon = match($nivel) {
                        'prioritario' => 'fa-fire',
                        'revisao'     => 'fa-rotate',
                        default       => 'fa-compass',
                    };
                @endphp

                <div class="trilha-step nivel-{{ $nivel }}">
                    {{-- Step number circle --}}
                    <div class="trilha-step-num">{{ $index + 1 }}</div>

                    {{-- Card --}}
                    <div class="trilha-card">
                        <div class="trilha-card-top">
                            <h3 class="trilha-card-title">{{ $topic->nome }}</h3>
                            <span class="badge-nivel {{ $nivel }}">
                                <i class="fa-solid {{ $badgeIcon }}"></i>
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        {{-- Meta info --}}
                        <div class="trilha-meta">
                            @if($perf && $pct !== null)
                                <span>
                                    <i class="fa-solid fa-bullseye"></i>
                                    <span class="perf-acertos {{ $pctClass }}">{{ $pct }}% de acertos</span>
                                </span>
                            @else
                                <span>
                                    <i class="fa-solid fa-circle-xmark" style="color: var(--text-secondary);"></i>
                                    Nunca acessado
                                </span>
                            @endif

                            <span>
                                <i class="fa-solid fa-file-lines"></i>
                                {{ $topic->contents_count }} conteúdo{{ $topic->contents_count !== 1 ? 's' : '' }}
                            </span>

                            <span>
                                <i class="fa-solid fa-circle-question"></i>
                                {{ $topic->quizzes_count }} quiz{{ $topic->quizzes_count !== 1 ? 'zes' : '' }}
                            </span>

                            @if($recs > 0)
                                <span class="rec-pill">
                                    <i class="fa-solid fa-lightbulb"></i>
                                    {{ $recs }} recomendaç{{ $recs !== 1 ? 'ões' : 'ão' }} pendente{{ $recs !== 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="trilha-actions">
                            <a href="{{ route('topico.show', $topic) }}" class="btn btn-primary">
                                <i class="fa-solid fa-book-open"></i> Ver Conteúdos
                            </a>
                            <a href="{{ route('topico.show', $topic) }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-circle-question"></i> Fazer Quiz
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

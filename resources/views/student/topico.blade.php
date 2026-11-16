@extends('layouts.app')

@section('title', '{{ $topic->nome }} — Estudaí')

@section('content')

{{-- ─── Breadcrumb ──────────────────────────────────────────────────────────── --}}
<div style="display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:var(--text-secondary); margin-bottom:1.5rem;">
    <a href="{{ route('home') }}" style="color:var(--text-secondary); text-decoration:none;">Início</a>
    <i class="fa-solid fa-chevron-right" style="font-size:0.65rem;"></i>
    <a href="{{ route('topicos') }}" style="color:var(--text-secondary); text-decoration:none;">Tópicos</a>
    <i class="fa-solid fa-chevron-right" style="font-size:0.65rem;"></i>
    <span style="color:var(--text-primary); font-weight:600;">{{ $topic->nome }}</span>
</div>

{{-- ─── Cabeçalho do tópico ─────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:2rem; background:linear-gradient(135deg, #3b2f7e 0%, #5e5ce6 60%, #a855f7 100%); border:none; padding:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <div style="display:flex; align-items:center; gap:0.8rem; margin-bottom:0.6rem;">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff;">
                    <i class="fa-solid {{ $topicIcon }}"></i>
                </div>
                <div>
                    <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">{{ $topic->nome }}</h2>
                    <p style="color:rgba(255,255,255,0.75); font-size:0.85rem; margin:0;">
                        {{ $topic->contents->count() }} conteúdo(s) disponível(eis) • {{ $topic->quizzes->count() }} quiz(zes)
                    </p>
                </div>
            </div>
            @if($topic->descricao)
                <p style="color:rgba(255,255,255,0.85); font-size:0.9rem; max-width:600px; margin:0;">{{ $topic->descricao }}</p>
            @endif
        </div>
        @if($performance)
            @php
                $pct = round($performance->percentual_acertos);
                $label = match($performance->classificacao) {
                    'dominado'           => 'Dominado ✓',
                    'revisao_leve'       => 'Revisão Leve',
                    'estudo_prioritario' => 'Estudo Prioritário',
                    default              => '—',
                };
            @endphp
            <div style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); border-radius:12px; padding:1rem 1.5rem; text-align:center; min-width:140px;">
                <div style="font-size:2rem; font-weight:800; color:#fff; line-height:1;">{{ $pct }}%</div>
                <div style="font-size:0.75rem; color:rgba(255,255,255,0.8); margin-top:0.3rem;">{{ $label }}</div>
                <div class="progress-bar-bg" style="margin-top:0.6rem; background:rgba(255,255,255,0.2);">
                    <div class="progress-bar-fill" style="width:{{ $pct }}%; background:#fff;"></div>
                </div>
            </div>
        @else
            <div style="background:rgba(255,255,255,0.1); border:1px dashed rgba(255,255,255,0.4); border-radius:12px; padding:1rem 1.5rem; text-align:center; color:rgba(255,255,255,0.7); font-size:0.85rem;">
                <i class="fa-solid fa-play" style="font-size:1.3rem; margin-bottom:0.4rem; display:block;"></i>
                Sem dados ainda.<br>Faça um quiz!
            </div>
        @endif
    </div>
</div>

{{-- ─── Conteúdos + Quizzes ─────────────────────────────────────────────────── --}}
<div class="grid-2" style="align-items:start;">

    {{-- Coluna esquerda: Conteúdos de Estudo --}}
    <div>
        <h3 class="section-title"><i class="fa-solid fa-file-lines" style="color:var(--primary);"></i> Materiais de Estudo</h3>

        @forelse($topic->contents as $content)
            <div class="card" style="margin-bottom:1rem; padding:1.2rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.4rem;">
                            <div style="width:32px; height:32px; border-radius:8px; background:var(--primary-light); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:0.85rem; flex-shrink:0;">
                                @if($content->url && str_contains($content->url, 'youtube'))
                                    <i class="fa-brands fa-youtube"></i>
                                @elseif($content->url)
                                    <i class="fa-solid fa-link"></i>
                                @else
                                    <i class="fa-solid fa-file-text"></i>
                                @endif
                            </div>
                            <h4 style="font-size:0.95rem; font-weight:700; margin:0;">{{ $content->titulo }}</h4>
                        </div>
                        @if($content->descricao)
                            <p style="font-size:0.83rem; color:var(--text-secondary); margin:0 0 0.6rem 0;">{{ $content->descricao }}</p>
                        @endif
                    </div>
                    @if($content->url)
                        <a href="{{ $content->url }}" target="_blank"
                            class="btn btn-primary" style="font-size:0.8rem; padding:0.5rem 0.9rem; flex-shrink:0; white-space:nowrap;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center; padding:2.5rem; color:var(--text-secondary);">
                <i class="fa-solid fa-file-circle-question" style="font-size:2rem; display:block; margin-bottom:0.8rem; opacity:0.4;"></i>
                <p>Nenhum material cadastrado para este tópico.</p>
            </div>
        @endforelse
    </div>

    {{-- Coluna direita: Quizzes disponíveis --}}
    <div>
        <h3 class="section-title"><i class="fa-solid fa-circle-question" style="color:var(--primary);"></i> Quizzes do Tópico</h3>

        @forelse($topic->quizzes as $quiz)
            @php
                $lastResult = $quizResults->where('quiz_id', $quiz->id)->first();
                $qs = $lastResult ? round($lastResult->pontuacao_percentual) : null;
                $bc = $qs >= 80 ? 'badge-dominado' : ($qs >= 50 ? 'badge-revisao' : 'badge-prioritario');
            @endphp
            <div class="card" style="margin-bottom:1rem; padding:1.2rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                            <div style="width:32px; height:32px; border-radius:8px; background:rgba(168,85,247,0.15); display:flex; align-items:center; justify-content:center; color:#a855f7; font-size:0.85rem; flex-shrink:0;">
                                <i class="fa-solid fa-circle-question"></i>
                            </div>
                            <h4 style="font-size:0.95rem; font-weight:700; margin:0;">{{ $quiz->titulo }}</h4>
                        </div>
                        @if($quiz->descricao)
                            <p style="font-size:0.83rem; color:var(--text-secondary); margin:0 0 0.5rem 0;">{{ Str::limit($quiz->descricao, 100) }}</p>
                        @endif
                        <div style="font-size:0.78rem; color:var(--text-secondary); display:flex; gap:1rem;">
                            <span><i class="fa-solid fa-list-ol"></i> {{ $quiz->questions->count() }} questão(ões)</span>
                            @if($lastResult)
                                <span>
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    Última vez: <span class="rec-badge {{ $bc }}" style="font-size:0.68rem; padding:0.1rem 0.5rem;">{{ $qs }}%</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.4rem; align-items:flex-end; flex-shrink:0;">
                        <a href="{{ route('quiz.show', $quiz) }}" class="btn btn-primary" style="font-size:0.8rem; padding:0.5rem 0.9rem; white-space:nowrap;">
                            <i class="fa-solid fa-{{ $lastResult ? 'rotate-right' : 'play' }}"></i>
                            {{ $lastResult ? 'Refazer' : 'Começar' }}
                        </a>
                        @if($lastResult)
                            <span style="font-size:0.72rem; color:var(--text-muted);">{{ $lastResult->created_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center; padding:2.5rem; color:var(--text-secondary);">
                <i class="fa-solid fa-circle-question" style="font-size:2rem; display:block; margin-bottom:0.8rem; opacity:0.4;"></i>
                <p>Nenhum quiz disponível para este tópico ainda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection

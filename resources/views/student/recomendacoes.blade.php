@extends('layouts.app')

@section('title', 'Recomendações — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-lightbulb" style="color:var(--warning);"></i> Minhas Recomendações
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Conteúdos recomendados com base no seu desempenho nos quizzes.</p>
    </div>
    @if($pendentes->count() > 0)
        <span style="background:var(--warning-light); color:var(--warning); border:1px solid var(--warning); border-radius:20px; padding:0.5rem 1rem; font-size:0.85rem; font-weight:700;">
            {{ $pendentes->count() }} pendente(s)
        </span>
    @endif
</div>

@if($pendentes->count() === 0 && $concluidas->count() === 0)
    <div class="card" style="text-align:center; padding:4rem; color:var(--text-secondary);">
        <i class="fa-solid fa-circle-check" style="font-size:3rem; color:var(--success); display:block; margin-bottom:1rem;"></i>
        <h3 style="margin-bottom:0.5rem;">Tudo em dia!</h3>
        <p>Você não tem recomendações pendentes. Continue fazendo quizzes para manter seu desempenho!</p>
        <a href="{{ route('topicos') }}" class="btn btn-primary" style="margin-top:1rem; display:inline-flex;">
            <i class="fa-solid fa-book-open"></i> Ver Tópicos
        </a>
    </div>
@else

    {{-- Pendentes --}}
    @if($pendentes->count() > 0)
        <h3 class="section-title"><i class="fa-solid fa-clock" style="color:var(--warning);"></i> Pendentes</h3>
        <div style="display:flex; flex-direction:column; gap:1rem; margin-bottom:2.5rem;">
            @foreach($pendentes as $rec)
                @php
                    $status = $performances->get($rec->topic_id)?->classificacao ?? 'estudo_prioritario';
                    $bc     = match($status) {
                        'revisao_leve' => 'badge-revisao',
                        'dominado'     => 'badge-dominado',
                        default        => 'badge-prioritario',
                    };
                    $bl = match($status) {
                        'revisao_leve' => 'Revisão Leve',
                        'dominado'     => 'Dominado',
                        default        => 'Estudo Prioritário',
                    };
                @endphp
                <div class="card" style="padding:1.2rem;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.5rem; flex-wrap:wrap;">
                                <span class="rec-badge {{ $bc }}">{{ $bl }}</span>
                                <span style="font-size:0.8rem; color:var(--text-secondary); font-weight:600;">
                                    <i class="fa-solid fa-bookmark"></i> {{ $rec->topic->nome }}
                                </span>
                            </div>
                            <h4 style="font-size:1rem; font-weight:700; margin-bottom:0.4rem;">{{ $rec->content->titulo }}</h4>
                            <p style="font-size:0.83rem; color:var(--text-secondary); margin-bottom:0.6rem;">{{ $rec->motivo }}</p>
                            <span style="font-size:0.75rem; color:var(--text-muted);">
                                <i class="fa-solid fa-calendar"></i> Gerado {{ $rec->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:0.5rem; align-items:flex-end; flex-shrink:0;">
                            @if($rec->content->url)
                                <a href="{{ $rec->content->url }}" target="_blank" class="btn btn-primary" style="font-size:0.85rem; white-space:nowrap;">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Estudar agora
                                </a>
                            @endif
                            <a href="{{ route('assistant', ['rec_id' => $rec->id]) }}" class="btn" style="font-size:0.85rem; background:var(--primary-light); color:var(--primary); border:none; white-space:nowrap;">
                                <i class="fa-solid fa-robot"></i> Perguntar ao assistente
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Concluídas --}}
    @if($concluidas->count() > 0)
        <h3 class="section-title"><i class="fa-solid fa-circle-check" style="color:var(--success);"></i> Concluídas</h3>
        <div style="display:flex; flex-direction:column; gap:0.8rem;">
            @foreach($concluidas as $rec)
                <div class="card" style="padding:1rem; opacity:0.7;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                        <div>
                            <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:0.2rem;">{{ $rec->topic->nome }}</div>
                            <h4 style="font-size:0.9rem; font-weight:600; margin:0;">{{ $rec->content->titulo }}</h4>
                        </div>
                        <span style="color:var(--success); font-size:0.8rem; font-weight:600; white-space:nowrap;">
                            <i class="fa-solid fa-circle-check"></i> Concluída
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endif
@endsection

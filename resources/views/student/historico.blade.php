@extends('layouts.app')

@section('title', 'Histórico de Quizzes — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);"></i> Histórico de Quizzes
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Todos os quizzes que você realizou na plataforma.</p>
    </div>
    @if($resultados->total() > 0)
        <div style="text-align:right;">
            <div style="font-size:1.5rem; font-weight:800; color:var(--primary);">{{ $resultados->total() }}</div>
            <div style="font-size:0.8rem; color:var(--text-secondary);">quizzes realizados</div>
        </div>
    @endif
</div>

@if($resultados->count() === 0)
    <div class="card" style="text-align:center; padding:4rem; color:var(--text-secondary);">
        <i class="fa-solid fa-hourglass" style="font-size:3rem; display:block; margin-bottom:1rem; opacity:0.4;"></i>
        <h3 style="margin-bottom:0.5rem;">Nenhum quiz realizado</h3>
        <p>Vá para Tópicos e faça seu primeiro quiz!</p>
        <a href="{{ route('topicos') }}" class="btn btn-primary" style="margin-top:1rem; display:inline-flex;">
            <i class="fa-solid fa-play"></i> Começar agora
        </a>
    </div>
@else
    <div class="card" style="padding:0; overflow:hidden; margin-bottom:1.5rem;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                        <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Quiz</th>
                        <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Tópico</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Acertos</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Pontuação</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Classificação</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Data</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $result)
                        @php
                            $sc = round($result->pontuacao_percentual);
                            $bc = $sc >= 80 ? 'badge-dominado' : ($sc >= 50 ? 'badge-revisao' : 'badge-prioritario');
                            $cl = $sc >= 80 ? 'Dominado' : ($sc >= 50 ? 'Revisão Leve' : 'Prioritário');
                        @endphp
                        <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem 1.5rem;">
                                <div style="font-weight:600; font-size:0.9rem;">{{ $result->quiz->titulo }}</div>
                            </td>
                            <td style="padding:1rem 1.5rem;">
                                <span style="background:var(--primary-light); color:var(--primary); border-radius:20px; padding:0.25rem 0.7rem; font-size:0.78rem; font-weight:600;">
                                    {{ $result->quiz->topic->nome ?? '—' }}
                                </span>
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center; font-weight:600;">
                                {{ $result->total_acertos }}/{{ $result->total_questoes }}
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                <span class="rec-badge {{ $bc }}">{{ $sc }}%</span>
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center; font-size:0.85rem; color:var(--text-secondary);">
                                {{ $cl }}
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">
                                {{ $result->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                <a href="{{ route('quiz.show', $result->quiz_id) }}"
                                   style="background:var(--primary-light); color:var(--primary); border:none; border-radius:8px; padding:0.4rem 0.7rem; font-size:0.8rem; font-weight:600; text-decoration:none; white-space:nowrap;">
                                    <i class="fa-solid fa-rotate-right"></i> Refazer
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginação --}}
    @if($resultados->hasPages())
        <div style="display:flex; justify-content:center;">
            {{ $resultados->links() }}
        </div>
    @endif
@endif
@endsection

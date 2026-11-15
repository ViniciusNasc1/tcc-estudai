@extends('layouts.app')

@section('title', 'Painel do Administrador — Estudaí')

@section('content')
{{-- ─── Welcome Banner ──────────────────────────────────────────────────────── --}}
<div class="welcome-banner" style="background: linear-gradient(135deg, #3b2f7e 0%, #5e5ce6 60%, #a855f7 100%);">
    <div class="welcome-info">
        <h2 style="display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-shield-halved" style="font-size:1.4rem;"></i>
            Painel do Administrador
        </h2>
        <p>Visão geral da plataforma Estudaí — <strong>{{ now()->format('d/m/Y') }}</strong></p>
    </div>
    <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
        <span style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:8px; padding:0.5rem 1rem; font-size:0.85rem; display:flex; align-items:center; gap:0.4rem;">
            <i class="fa-solid fa-circle" style="color:#30d158; font-size:0.5rem;"></i> Sistema Online
        </span>
    </div>
</div>

{{-- ─── Cards de Métricas Globais ───────────────────────────────────────────── --}}
<div class="grid-4" style="margin-bottom:2.5rem;">
    {{-- Card 1: Estudantes --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--primary-light); color:var(--primary);">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalEstudantes }}</span>
            <span class="stat-label">Estudantes cadastrados</span>
        </div>
    </div>

    {{-- Card 2: Quizzes disponíveis --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--success-light); color:var(--success);">
            <i class="fa-solid fa-circle-question"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalQuizzesDisponiveis }}</span>
            <span class="stat-label">Quizzes disponíveis</span>
        </div>
    </div>

    {{-- Card 3: Média Geral --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:rgba(168,85,247,0.15); color:#a855f7;">
            <i class="fa-solid fa-chart-pie"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ round($mediaGeralPlataforma) }}%</span>
            <span class="stat-label">Média geral da plataforma</span>
        </div>
    </div>

    {{-- Card 4: Recomendações --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--warning-light); color:var(--warning);">
            <i class="fa-solid fa-lightbulb"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalRecomendacoes }}</span>
            <span class="stat-label">Recomendações geradas</span>
        </div>
    </div>
</div>

{{-- ─── Desempenho por Tópico (Plataforma) ─────────────────────────────────── --}}
<div class="grid-2" style="margin-bottom:2.5rem;">
    <div class="card">
        <h4 style="margin-bottom:1.5rem; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-chart-bar" style="color:var(--primary);"></i> Desempenho Médio por Tópico
        </h4>
        @if($desempenhoTopicos->count() > 0)
            <div style="display:flex; flex-direction:column; gap:1rem;">
                @foreach($desempenhoTopicos as $perf)
                    @php
                        $media = round($perf->media_acertos);
                        $color = $media >= 80 ? 'var(--success)' : ($media >= 50 ? 'var(--warning)' : 'var(--danger)');
                    @endphp
                    <div>
                        <div class="progress-labels">
                            <span style="font-size:0.9rem; font-weight:500;">{{ $perf->topic->nome }}</span>
                            <strong style="color:{{ $color }};">{{ $media }}%</strong>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width:{{ $media }}%; background:{{ $color }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center; color:var(--text-secondary); padding:2rem;">
                <i class="fa-solid fa-hourglass" style="font-size:2rem; margin-bottom:0.8rem;"></i>
                <p>Nenhum dado de desempenho ainda.</p>
            </div>
        @endif
    </div>

    {{-- Acesso rápido aos módulos --}}
    <div class="card" style="display:flex; flex-direction:column; gap:0.8rem;">
        <h4 style="font-weight:600; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-bolt" style="color:#a855f7;"></i> Acesso Rápido
        </h4>
        <a href="{{ route('admin.usuarios') }}" style="display:flex; justify-content:space-between; align-items:center; padding:0.8rem; background:var(--bg-primary); border-radius:8px; border:1px solid var(--border-color); text-decoration:none; color:var(--text-primary); transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-users" style="color:var(--primary);"></i> Gerenciar Usuários</span>
            <i class="fa-solid fa-arrow-right" style="color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
        <a href="{{ route('admin.topicos') }}" style="display:flex; justify-content:space-between; align-items:center; padding:0.8rem; background:var(--bg-primary); border-radius:8px; border:1px solid var(--border-color); text-decoration:none; color:var(--text-primary); transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-book-bookmark" style="color:#a855f7;"></i> Gerenciar Tópicos</span>
            <i class="fa-solid fa-arrow-right" style="color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
        <a href="{{ route('admin.conteudos') }}" style="display:flex; justify-content:space-between; align-items:center; padding:0.8rem; background:var(--bg-primary); border-radius:8px; border:1px solid var(--border-color); text-decoration:none; color:var(--text-primary); transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-file-lines" style="color:var(--success);"></i> Gerenciar Conteúdos</span>
            <i class="fa-solid fa-arrow-right" style="color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
        <a href="{{ route('admin.quizzes') }}" style="display:flex; justify-content:space-between; align-items:center; padding:0.8rem; background:var(--bg-primary); border-radius:8px; border:1px solid var(--border-color); text-decoration:none; color:var(--text-primary); transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-circle-question" style="color:var(--warning);"></i> Gerenciar Quizzes</span>
            <i class="fa-solid fa-arrow-right" style="color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
        <a href="{{ route('admin.relatorios') }}" style="display:flex; justify-content:space-between; align-items:center; padding:0.8rem; background:var(--bg-primary); border-radius:8px; border:1px solid var(--border-color); text-decoration:none; color:var(--text-primary); transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <span style="display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-chart-pie" style="color:#ef4444;"></i> Ver Relatórios</span>
            <i class="fa-solid fa-arrow-right" style="color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
    </div>
</div>

{{-- ─── Tabela de Estudantes ────────────────────────────────────────────────── --}}
<div>
    <h3 class="section-title">
        <i class="fa-solid fa-table-list" style="color:var(--primary);"></i> Estudantes Cadastrados
    </h3>

    <div class="card" style="padding:0; overflow:hidden;">
        @if($estudantes->count() > 0)
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                            <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Estudante</th>
                            <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">E-mail</th>
                            <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Quizzes</th>
                            <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Média</th>
                            <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Rec. Pendentes</th>
                            <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estudantes as $index => $estudante)
                            @php
                                $media = round($estudante->media_desempenho);
                                $mediaColor = $media >= 80 ? 'var(--success)' : ($media >= 50 ? 'var(--warning)' : 'var(--danger)');
                                $mediaClass = $media >= 80 ? 'badge-dominado' : ($media >= 50 ? 'badge-revisao' : 'badge-prioritario');
                                $names = explode(' ', $estudante->name);
                                $initials = count($names) >= 2
                                    ? strtoupper(substr($names[0],0,1).substr($names[count($names)-1],0,1))
                                    : strtoupper(substr($names[0],0,2));
                            @endphp
                            <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:1rem 1.5rem;">
                                    <div style="display:flex; align-items:center; gap:0.8rem;">
                                        <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#a855f7); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; color:#fff; flex-shrink:0;">
                                            {{ $initials }}
                                        </div>
                                        <span style="font-weight:600;">{{ $estudante->name }}</span>
                                    </div>
                                </td>
                                <td style="padding:1rem 1.5rem; color:var(--text-secondary); font-size:0.9rem;">{{ $estudante->email }}</td>
                                <td style="padding:1rem 1.5rem; text-align:center; font-weight:600;">{{ $estudante->total_quizzes }}</td>
                                <td style="padding:1rem 1.5rem; text-align:center;">
                                    @if($estudante->total_quizzes > 0)
                                        <span class="rec-badge {{ $mediaClass }}">{{ $media }}%</span>
                                    @else
                                        <span style="color:var(--text-secondary); font-size:0.85rem;">—</span>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.5rem; text-align:center;">
                                    @if($estudante->recomendacoes_pendentes > 0)
                                        <span style="background:rgba(255,214,10,0.15); color:var(--warning); border-radius:20px; padding:0.3rem 0.8rem; font-size:0.8rem; font-weight:600;">
                                            {{ $estudante->recomendacoes_pendentes }}
                                        </span>
                                    @else
                                        <span style="color:var(--success); font-size:0.85rem;">
                                            <i class="fa-solid fa-circle-check"></i> Nenhuma
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.5rem; text-align:center;">
                                    <span style="display:inline-flex; align-items:center; gap:0.3rem; background:rgba(48,209,88,0.1); color:var(--success); border-radius:20px; padding:0.3rem 0.8rem; font-size:0.8rem; font-weight:600;">
                                        <i class="fa-solid fa-circle" style="font-size:0.4rem;"></i> Ativo
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center; color:var(--text-secondary); padding:3rem;">
                <i class="fa-solid fa-users-slash" style="font-size:2.5rem; margin-bottom:1rem; opacity:0.4;"></i>
                <p>Nenhum estudante cadastrado ainda.</p>
            </div>
        @endif
    </div>
</div>
@endsection

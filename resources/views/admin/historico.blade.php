@extends('layouts.app')

@section('title', 'Histórico de Atividades — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-timeline" style="color:var(--primary);"></i> Histórico de Atividades
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Registro completo de todas as ações realizadas na plataforma.</p>
    </div>
    <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Voltar
    </a>
</div>

{{-- Legenda de tipos de ação --}}
<div style="display:flex; gap:0.8rem; flex-wrap:wrap; margin-bottom:1.5rem;">
    @php
        $acoes = [
            'login'               => ['icon' => 'fa-right-to-bracket', 'color' => 'var(--success)',  'label' => 'Login'],
            'logout'              => ['icon' => 'fa-right-from-bracket','color' => 'var(--text-secondary)', 'label' => 'Logout'],
            'quiz_realizado'      => ['icon' => 'fa-circle-question',   'color' => 'var(--primary)', 'label' => 'Quiz realizado'],
            'recomendacao_gerada' => ['icon' => 'fa-lightbulb',         'color' => 'var(--warning)', 'label' => 'Recomendação gerada'],
            'conteudo_acessado'   => ['icon' => 'fa-file-lines',        'color' => '#a855f7',        'label' => 'Conteúdo acessado'],
            'relatorio_gerado'    => ['icon' => 'fa-chart-pie',         'color' => 'var(--danger)',  'label' => 'Relatório gerado'],
        ];
    @endphp
    @foreach($acoes as $tipo => $cfg)
        <span style="display:inline-flex; align-items:center; gap:0.4rem; background:var(--bg-card); border:1px solid var(--border-color); border-radius:20px; padding:0.4rem 0.9rem; font-size:0.8rem; font-weight:600; color:{{ $cfg['color'] }};">
            <i class="fa-solid {{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
        </span>
    @endforeach
</div>

{{-- Tabela de atividades --}}
<div class="card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">#</th>
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Usuário</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Ação</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Ref. ID</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Data / Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historico as $ativ)
                    @php
                        $cfg   = $acoes[$ativ->tipo_acao] ?? ['icon' => 'fa-circle', 'color' => 'var(--text-secondary)', 'label' => $ativ->tipo_acao];
                        $names = explode(' ', $ativ->usuario->name ?? '');
                        $initials = count($names) >= 2
                            ? strtoupper(substr($names[0],0,1).substr($names[count($names)-1],0,1))
                            : strtoupper(substr($names[0] ?? '?', 0, 2));
                    @endphp
                    <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">{{ $ativ->id }}</td>
                        <td style="padding:1rem 1.5rem;">
                            <div style="display:flex; align-items:center; gap:0.7rem;">
                                <div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#a855f7); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem; color:#fff; flex-shrink:0;">{{ $initials }}</div>
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem;">{{ $ativ->usuario->name ?? '—' }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-secondary);">{{ $ativ->usuario->tipo ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:0.4rem; background:var(--bg-primary); border:1px solid var(--border-color); border-radius:20px; padding:0.4rem 0.9rem; font-size:0.8rem; font-weight:600; color:{{ $cfg['color'] }};">
                                <i class="fa-solid {{ $cfg['icon'] }}"></i> {{ $cfg['label'] }}
                            </span>
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.9rem;">
                            {{ $ativ->referencia_id ?? '—' }}
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">
                            {{ $ativ->created_at->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:3rem; text-align:center; color:var(--text-secondary);">
                            <i class="fa-solid fa-timeline" style="font-size:2rem; display:block; margin-bottom:1rem; opacity:0.3;"></i>
                            Nenhuma atividade registrada ainda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Paginação --}}
    @if($historico->hasPages())
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border-color);">
            {{ $historico->links() }}
        </div>
    @endif
</div>
@endsection

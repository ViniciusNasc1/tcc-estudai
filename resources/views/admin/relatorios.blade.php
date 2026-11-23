@extends('layouts.app')

@section('title', 'Relatórios — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Relatórios da Plataforma
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Visão detalhada do desempenho geral dos estudantes.</p>
    </div>
    <div style="display:flex; gap:0.8rem; align-items:center;">
        {{-- Botão Gerar Relatório --}}
        <form action="{{ route('admin.relatorios.gerar') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-file-circle-plus"></i> Gerar Relatório
            </button>
        </form>
        <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

{{-- Flash de sucesso --}}
@if(session('success'))
    <div style="background:var(--success-light); border:1px solid var(--success); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--success); font-weight:600; display:flex; align-items:center; gap:0.6rem;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Métricas globais --}}
<div class="grid-4" style="margin-bottom:2rem;">
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--primary-light); color:var(--primary);"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalEstudantes }}</span>
            <span class="stat-label">Estudantes ativos</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--success-light); color:var(--success);"><i class="fa-solid fa-clipboard-check"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalQuizzes }}</span>
            <span class="stat-label">Quizzes realizados</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:rgba(168,85,247,0.15); color:#a855f7;"><i class="fa-solid fa-bullseye"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ round($mediaGeralPlataforma) }}%</span>
            <span class="stat-label">Média geral</span>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background-color:var(--warning-light); color:var(--warning);"><i class="fa-solid fa-lightbulb"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalRecomendacoes }}</span>
            <span class="stat-label">Recomendações geradas</span>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:2rem;">
    {{-- Gráfico de desempenho por tópico --}}
    <div class="card">
        <h4 style="margin-bottom:1.5rem; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-chart-bar" style="color:var(--primary);"></i> Desempenho Médio por Tópico
        </h4>
        <div style="position:relative; height:260px;">
            <canvas id="topicChartAdmin"></canvas>
        </div>
    </div>

    {{-- Gráfico de evolução da plataforma --}}
    <div class="card">
        <h4 style="margin-bottom:1.5rem; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-chart-line" style="color:#a855f7;"></i> Evolução da Média Diária
        </h4>
        <div style="position:relative; height:260px;">
            <canvas id="evolutionChartAdmin"></canvas>
        </div>
    </div>
</div>

{{-- Tabela de estudantes com ranking --}}
<div>
    <h3 class="section-title"><i class="fa-solid fa-ranking-star" style="color:var(--warning);"></i> Ranking de Estudantes</h3>
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">#</th>
                        <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Estudante</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Quizzes</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Média</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudantes as $index => $e)
                        @php
                            $media = round($e->media_desempenho);
                            $badgeClass = $media >= 80 ? 'badge-dominado' : ($media >= 50 ? 'badge-revisao' : 'badge-prioritario');
                            $names = explode(' ', $e->name);
                            $initials = count($names) >= 2 ? strtoupper(substr($names[0],0,1).substr($names[count($names)-1],0,1)) : strtoupper(substr($names[0],0,2));
                            $medal = ['🥇','🥈','🥉'][$index] ?? ($index + 1);
                        @endphp
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem 1.5rem; text-align:center; font-size:1.1rem;">{{ $medal }}</td>
                            <td style="padding:1rem 1.5rem;">
                                <div style="display:flex; align-items:center; gap:0.8rem;">
                                    <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#a855f7); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; color:#fff;">{{ $initials }}</div>
                                    <div>
                                        <div style="font-weight:600;">{{ $e->name }}</div>
                                        <div style="font-size:0.8rem; color:var(--text-secondary);">{{ $e->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center; font-weight:600;">{{ $e->total_quizzes }}</td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                @if($e->total_quizzes > 0)
                                    <span class="rec-badge {{ $badgeClass }}">{{ $media }}%</span>
                                @else
                                    <span style="color:var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                @if($media >= 80)
                                    <span style="color:var(--success); font-size:0.85rem; font-weight:600;"><i class="fa-solid fa-trophy"></i> Dominando</span>
                                @elseif($media >= 50)
                                    <span style="color:var(--warning); font-size:0.85rem; font-weight:600;"><i class="fa-solid fa-rotate"></i> Revisão</span>
                                @elseif($e->total_quizzes > 0)
                                    <span style="color:var(--danger); font-size:0.85rem; font-weight:600;"><i class="fa-solid fa-fire"></i> Prioritário</span>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.85rem;">Iniciante</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding:3rem; text-align:center; color:var(--text-secondary);">Nenhum estudante cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ─── Histórico de Relatórios Salvos ───────────────────────────────────────── --}}
<div style="margin-top:2.5rem;">
    <h3 class="section-title">
        <i class="fa-solid fa-clock-rotate-left" style="color:#a855f7;"></i> Histórico de Relatórios Gerados
    </h3>
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                        <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">#</th>
                        <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Título</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Tipo</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Gerado por</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Data</th>
                        <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Snapshot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relatoriosAnteriores as $rel)
                        <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem 1.5rem; color:var(--text-secondary); font-size:0.85rem;">{{ $rel->id }}</td>
                            <td style="padding:1rem 1.5rem; font-weight:600;">{{ $rel->titulo }}</td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                <span style="background:var(--primary-light); color:var(--primary); border-radius:20px; padding:0.3rem 0.8rem; font-size:0.75rem; font-weight:700; text-transform:capitalize;">{{ $rel->tipo }}</span>
                            </td>
                            <td style="padding:1rem 1.5rem; text-align:center; font-size:0.9rem;">{{ $rel->gerador->name ?? '—' }}</td>
                            <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">{{ $rel->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding:1rem 1.5rem; text-align:center;">
                                <button onclick="document.getElementById('snapshot-{{ $rel->id }}').style.display = document.getElementById('snapshot-{{ $rel->id }}').style.display === 'none' ? 'block' : 'none'"
                                    style="background:var(--primary-light); color:var(--primary); border:none; border-radius:8px; padding:0.4rem 0.8rem; cursor:pointer; font-size:0.8rem; font-weight:600;">
                                    <i class="fa-solid fa-eye"></i> Ver dados
                                </button>
                            </td>
                        </tr>
                        {{-- Linha expansível com snapshot JSON --}}
                        <tr id="snapshot-{{ $rel->id }}" style="display:none; background:var(--bg-primary);">
                            <td colspan="6" style="padding:1rem 1.5rem;">
                                <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:0.5rem; font-weight:600;">
                                    <i class="fa-solid fa-database"></i> Dados salvos no momento da geração:
                                </div>
                                <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                                    <div style="background:var(--bg-secondary); border-radius:8px; padding:0.8rem 1.2rem; border:1px solid var(--border-color);">
                                        <div style="font-size:0.75rem; color:var(--text-secondary);">Estudantes</div>
                                        <div style="font-weight:700; font-size:1.1rem;">{{ $rel->dados['total_estudantes'] ?? '—' }}</div>
                                    </div>
                                    <div style="background:var(--bg-secondary); border-radius:8px; padding:0.8rem 1.2rem; border:1px solid var(--border-color);">
                                        <div style="font-size:0.75rem; color:var(--text-secondary);">Quizzes realizados</div>
                                        <div style="font-weight:700; font-size:1.1rem;">{{ $rel->dados['total_quizzes'] ?? '—' }}</div>
                                    </div>
                                    <div style="background:var(--bg-secondary); border-radius:8px; padding:0.8rem 1.2rem; border:1px solid var(--border-color);">
                                        <div style="font-size:0.75rem; color:var(--text-secondary);">Média geral</div>
                                        <div style="font-weight:700; font-size:1.1rem;">{{ $rel->dados['media_geral'] ?? '—' }}%</div>
                                    </div>
                                    <div style="background:var(--bg-secondary); border-radius:8px; padding:0.8rem 1.2rem; border:1px solid var(--border-color);">
                                        <div style="font-size:0.75rem; color:var(--text-secondary);">Recomendações</div>
                                        <div style="font-weight:700; font-size:1.1rem;">{{ $rel->dados['total_recomendacoes'] ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:2rem; text-align:center; color:var(--text-secondary);">
                                Nenhum relatório gerado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Barras: Desempenho por Tópico
const ctxTopic = document.getElementById('topicChartAdmin').getContext('2d');
const topicNames  = {!! json_encode($desempenhoTopicos->pluck('topic.nome')) !!};
const topicScores = {!! json_encode($desempenhoTopicos->pluck('media_acertos')->map(fn($v) => round($v))) !!};

const bgColors = topicScores.map(s => s >= 80 ? 'rgba(48,209,88,0.75)' : (s >= 50 ? 'rgba(255,214,10,0.75)' : 'rgba(255,69,58,0.75)'));
const bdColors = topicScores.map(s => s >= 80 ? 'rgb(48,209,88)' : (s >= 50 ? 'rgb(255,214,10)' : 'rgb(255,69,58)'));

new Chart(ctxTopic, {
    type: 'bar',
    data: { labels: topicNames, datasets: [{ label: 'Média (%)', data: topicScores, backgroundColor: bgColors, borderColor: bdColors, borderWidth: 1.5, borderRadius: 8, barThickness: 36 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#98a0b3', font: { family: 'Outfit' } } }, x: { grid: { display: false }, ticks: { color: '#98a0b3', font: { family: 'Outfit', weight: '600' } } } } }
});

// Gráfico de Linha: Evolução da média diária
const ctxEvol = document.getElementById('evolutionChartAdmin').getContext('2d');
const evolData = {!! json_encode($quizzesPorDia) !!};

new Chart(ctxEvol, {
    type: 'line',
    data: { labels: evolData.map(d => d.dia), datasets: [{ label: 'Média do dia (%)', data: evolData.map(d => Math.round(d.media)), borderColor: '#a855f7', backgroundColor: 'rgba(168,85,247,0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#5e5ce6', pointBorderColor: '#fff', pointBorderWidth: 1.5, pointRadius: 5 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#98a0b3' } }, x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#98a0b3' } } } }
});
</script>
@endsection

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
        overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .trilha-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-1px);
    }
    .trilha-card-body {
        padding: 1.25rem 1.5rem;
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
        align-items: center;
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

    /* ── Botão Criar Trilha ── */
    .btn-criar-trilha {
        background: linear-gradient(135deg, #6366f1, var(--primary));
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: opacity 0.15s, transform 0.15s;
        font-weight: 600;
    }
    .btn-criar-trilha:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    .btn-criar-trilha:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* ── Toggle trilha IA ── */
    .trilha-toggle-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: background 0.15s, color 0.15s;
    }
    .trilha-toggle-btn:hover {
        background: var(--border-color);
        color: var(--text-primary);
    }
    .trilha-toggle-btn .arrow-icon {
        transition: transform 0.3s;
    }
    .trilha-toggle-btn.expanded .arrow-icon {
        transform: rotate(180deg);
    }

    /* ── Painel de trilha gerada pela IA ── */
    .trilha-ia-panel {
        border-top: 1px solid var(--border-color);
        background: rgba(99,102,241,0.03);
        display: none;
        padding: 1.25rem 1.5rem;
    }
    .trilha-ia-panel.visible {
        display: block;
    }
    .trilha-ia-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #6366f1;
        margin-bottom: 1rem;
    }

    /* ── Passos da trilha IA ── */
    .trilha-ia-steps {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .trilha-ia-step {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
    .trilha-ia-step-num {
        min-width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #6366f1;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .trilha-ia-step-content {
        flex: 1;
    }
    .trilha-ia-step-title {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--text-primary);
        margin-bottom: 0.2rem;
    }
    .trilha-ia-step-desc {
        font-size: 0.82rem;
        color: var(--text-secondary);
        margin-bottom: 0.3rem;
        line-height: 1.5;
    }
    .trilha-ia-step-meta {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .trilha-ia-step-badge {
        font-size: 0.72rem;
        padding: 0.15rem 0.5rem;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .badge-tipo-teoria   { background: rgba(99,102,241,0.12); color: #6366f1; border: 1px solid rgba(99,102,241,0.3); }
    .badge-tipo-pratica  { background: rgba(16,185,129,0.12); color: var(--success); border: 1px solid rgba(16,185,129,0.3); }
    .badge-tipo-revisao  { background: rgba(234,179,8,0.12); color: var(--warning); border: 1px solid rgba(234,179,8,0.3); }
    .badge-tipo-projeto  { background: rgba(239,68,68,0.12); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); }
    .badge-duracao {
        font-size: 0.72rem;
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* ── Loading spinner ── */
    .trilha-ia-loading {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-secondary);
        padding: 0.5rem 0;
    }
    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid var(--border-color);
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        flex-shrink: 0;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Error message ── */
    .trilha-ia-error {
        font-size: 0.82rem;
        color: var(--danger);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 0;
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
        Sua trilha é gerada com base no seu desempenho nos quizzes.
        Os tópicos que precisam de mais atenção aparecem primeiro.
        Clique em <strong>"Criar Trilha"</strong> para a IA gerar um plano detalhado de estudos para cada tópico.
    </p>

    {{-- ── Empty state ── --}}
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
                        <div class="trilha-card-body">
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

                                {{-- Botão criar trilha --}}
                                <button
                                    class="btn-criar-trilha"
                                    data-topic-id="{{ $topic->id }}"
                                    data-panel-id="trilha-ia-{{ $topic->id }}"
                                    onclick="gerarTrilha(this, {{ $topic->id }})"
                                    id="btn-criar-{{ $topic->id }}"
                                >
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Criar Trilha
                                </button>

                                {{-- Botão expandir (aparece após a trilha ser gerada) --}}
                                <button
                                    class="trilha-toggle-btn"
                                    id="btn-toggle-{{ $topic->id }}"
                                    style="display: none;"
                                    onclick="toggleTrilha(this, {{ $topic->id }})"
                                >
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                    Ver Trilha
                                </button>
                            </div>
                        </div>

                        {{-- Painel da trilha gerada pela IA --}}
                        <div class="trilha-ia-panel" id="trilha-ia-{{ $topic->id }}">
                            {{-- Conteúdo injetado via JS --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    // CSRF token para requisições POST
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    // Mapa de tipo para ícone e classe CSS
    const tipoMap = {
        teoria:  { icon: 'fa-book-open',    css: 'badge-tipo-teoria',  label: 'Teoria' },
        pratica: { icon: 'fa-code',          css: 'badge-tipo-pratica', label: 'Prática' },
        revisao: { icon: 'fa-rotate',        css: 'badge-tipo-revisao', label: 'Revisão' },
        projeto: { icon: 'fa-diagram-project', css: 'badge-tipo-projeto', label: 'Projeto' },
    };

    async function gerarTrilha(btn, topicId) {
        const panel   = document.getElementById('trilha-ia-' + topicId);
        const toggleBtn = document.getElementById('btn-toggle-' + topicId);

        // Desabilitar botão e mostrar loading
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="border-color: rgba(255,255,255,0.3); border-top-color: #fff;"></span> Gerando...';

        // Mostrar painel com loading
        panel.classList.add('visible');
        panel.innerHTML = `
            <div class="trilha-ia-loading">
                <div class="spinner"></div>
                A IA está criando sua trilha personalizada...
            </div>
        `;

        try {
            const response = await fetch('/trilha/gerar/' + topicId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error ?? 'Erro ao gerar trilha.');
            }

            // Renderizar os passos
            renderTrilha(panel, data.passos);

            // Atualizar botão criar → "Recriar Trilha"
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Recriar Trilha';

            // Mostrar botão de toggle
            toggleBtn.style.display = 'inline-flex';
            toggleBtn.classList.add('expanded');
            toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-down arrow-icon" style="transform: rotate(180deg);"></i> Ocultar Trilha';

        } catch (err) {
            panel.innerHTML = `
                <div class="trilha-ia-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    ${err.message ?? 'Erro ao conectar com a IA. Tente novamente.'}
                </div>
            `;
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Criar Trilha';
            toggleBtn.style.display = 'none';
        }
    }

    function renderTrilha(panel, passos) {
        const stepsHtml = passos.map(passo => {
            const tipo  = (passo.tipo ?? 'teoria').toLowerCase();
            const tInfo = tipoMap[tipo] ?? tipoMap['teoria'];

            return `
                <div class="trilha-ia-step">
                    <div class="trilha-ia-step-num">${passo.ordem}</div>
                    <div class="trilha-ia-step-content">
                        <div class="trilha-ia-step-title">${escapeHtml(passo.titulo)}</div>
                        <div class="trilha-ia-step-desc">${escapeHtml(passo.descricao)}</div>
                        <div class="trilha-ia-step-meta">
                            <span class="trilha-ia-step-badge ${tInfo.css}">
                                <i class="fa-solid ${tInfo.icon}"></i>
                                ${tInfo.label}
                            </span>
                            <span class="badge-duracao">
                                <i class="fa-regular fa-clock"></i>
                                ${escapeHtml(passo.duracao_estimada ?? '')}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        panel.innerHTML = `
            <div class="trilha-ia-header">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Trilha de Estudos gerada pela IA
            </div>
            <div class="trilha-ia-steps">
                ${stepsHtml}
            </div>
        `;
    }

    function toggleTrilha(btn, topicId) {
        const panel = document.getElementById('trilha-ia-' + topicId);
        const isExpanded = btn.classList.contains('expanded');

        if (isExpanded) {
            btn.classList.remove('expanded');
            btn.innerHTML = '<i class="fa-solid fa-chevron-down arrow-icon"></i> Ver Trilha';
            panel.classList.remove('visible');
        } else {
            btn.classList.add('expanded');
            btn.innerHTML = '<i class="fa-solid fa-chevron-down arrow-icon" style="transform: rotate(180deg);"></i> Ocultar Trilha';
            panel.classList.add('visible');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text ?? ''));
        return div.innerHTML;
    }
</script>
@endsection

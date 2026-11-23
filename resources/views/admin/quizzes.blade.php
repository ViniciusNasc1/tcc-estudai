@extends('layouts.app')

@section('title', 'Gerenciar Quizzes — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-circle-question" style="color:var(--primary);"></i> Gerenciar Quizzes
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Crie quizzes, adicione questões e gerencie as alternativas.</p>
    </div>
    <div style="display:flex; gap:0.8rem;">
        <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <button onclick="document.getElementById('modal-novo-quiz').style.display='flex'" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Novo Quiz
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background:var(--success-light); border:1px solid var(--success); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--success); display:flex; align-items:center; gap:0.6rem;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:var(--danger-light); border:1px solid var(--danger); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--danger); display:flex; align-items:center; gap:0.6rem;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
@endif

{{-- Lista de Quizzes com Accordion de Questões --}}
<div style="display:flex; flex-direction:column; gap:1.2rem;">
    @forelse($quizzes as $quiz)
        <div class="card" style="padding:0; overflow:hidden;">
            {{-- Cabeçalho do quiz --}}
            <div style="padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:center; cursor:pointer; user-select:none;" onclick="toggleAccordion('quiz-{{ $quiz->id }}')">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="width:42px; height:42px; border-radius:10px; background:var(--primary-light); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:1.1rem;">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:1rem;">{{ $quiz->titulo }}</div>
                        <div style="font-size:0.8rem; color:var(--text-secondary); display:flex; gap:1rem; margin-top:0.2rem;">
                            <span><i class="fa-solid fa-bookmark"></i> {{ $quiz->topic->nome }}</span>
                            <span><i class="fa-solid fa-list-ol"></i> {{ $quiz->questions->count() }} questão(ões)</span>
                        </div>
                    </div>
                </div>
                <div style="display:flex; gap:0.6rem; align-items:center;">
                    <button onclick="event.stopPropagation(); openEditQuiz({{ $quiz->id }}, '{{ addslashes($quiz->titulo) }}', '{{ addslashes($quiz->descricao ?? '') }}', {{ $quiz->topic_id }})"
                        style="background:var(--primary-light); color:var(--primary); border:none; border-radius:8px; padding:0.4rem 0.7rem; cursor:pointer;">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Excluir quiz {{ addslashes($quiz->titulo) }} e todas as suas questões?');" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="event.stopPropagation()" style="background:var(--danger-light); color:var(--danger); border:none; border-radius:8px; padding:0.4rem 0.7rem; cursor:pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                    <i class="fa-solid fa-chevron-down accordion-icon" id="icon-quiz-{{ $quiz->id }}" style="color:var(--text-secondary); transition:transform 0.3s;"></i>
                </div>
            </div>

            {{-- Corpo do accordion: questões --}}
            <div id="quiz-{{ $quiz->id }}" style="display:none; border-top:1px solid var(--border-color);">
                <div style="padding:1.5rem;">
                    @if($quiz->questions->count() > 0)
                        <div style="display:flex; flex-direction:column; gap:1rem; margin-bottom:1.5rem;">
                            @foreach($quiz->questions as $qi => $questao)
                                <div style="background:var(--bg-primary); border:1px solid var(--border-color); border-radius:10px; padding:1rem 1.2rem;">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.8rem;">
                                        <div style="font-weight:600; font-size:0.95rem;">
                                            <span style="color:var(--primary); margin-right:0.4rem;">Q{{ $qi+1 }}.</span> {{ $questao->enunciado }}
                                        </div>
                                        <form action="{{ route('admin.questoes.destroy', $questao) }}" method="POST" onsubmit="return confirm('Excluir esta questão?');" style="margin:0; flex-shrink:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:0.85rem;" title="Excluir questão">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Conteúdo vinculado --}}
                                    @if($questao->content)
                                        <div style="margin-bottom:0.6rem; font-size:0.78rem; display:flex; align-items:center; gap:0.4rem; color:#6366f1;">
                                            <i class="fa-solid fa-link"></i>
                                            <span>Recomenda: <strong>{{ $questao->content->titulo }}</strong></span>
                                        </div>
                                    @else
                                        <div style="margin-bottom:0.6rem; font-size:0.78rem; color:var(--text-muted);">
                                            <i class="fa-solid fa-link-slash"></i> Sem conteúdo vinculado (usará fallback do tópico)
                                        </div>
                                    @endif
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        @foreach($questao->alternatives as $alt)
                                            <div style="display:flex; align-items:center; gap:0.6rem; font-size:0.85rem; padding:0.4rem 0.6rem; border-radius:6px; background:{{ $alt->correta ? 'rgba(48,209,88,0.1)' : 'transparent' }}; border:1px solid {{ $alt->correta ? 'var(--success)' : 'var(--border-color)' }};">
                                                @if($alt->correta)
                                                    <i class="fa-solid fa-circle-check" style="color:var(--success);"></i>
                                                @else
                                                    <i class="fa-regular fa-circle" style="color:var(--text-muted);"></i>
                                                @endif
                                                {{ $alt->descricao }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:1.5rem;">
                            <i class="fa-solid fa-info-circle"></i> Nenhuma questão cadastrada neste quiz.
                        </p>
                    @endif

                    {{-- Formulário: Adicionar questão --}}
                    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:12px; padding:1.2rem;">
                        <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:1rem; color:var(--text-primary);">
                            <i class="fa-solid fa-plus" style="color:var(--primary);"></i> Adicionar Questão
                        </h4>
                        <form action="{{ route('admin.questoes.store', $quiz) }}" method="POST" id="form-questao-{{ $quiz->id }}">
                            @csrf
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:500; color:var(--text-secondary);">Enunciado da questão *</label>
                                <textarea name="enunciado" class="form-control" style="padding:0.7rem 0.9rem; min-height:70px; resize:vertical; font-size:0.9rem;" placeholder="Digite a pergunta..." required></textarea>
                            </div>

                            <input type="hidden" name="alternativa_correta" id="correta-{{ $quiz->id }}" value="0">

                            {{-- Vínculo de conteúdo específico --}}
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:500; color:var(--text-secondary);">
                                    <i class="fa-solid fa-link" style="color:#6366f1;"></i>
                                    Conteúdo vinculado
                                    <span style="font-weight:400; color:var(--text-muted)">(opcional — recomendado ao aluno se ele errar esta questão)</span>
                                </label>
                                <select name="content_id" class="form-control" style="padding:0.6rem 0.9rem; font-size:0.85rem;">
                                    <option value="">— Nenhum (usa todos os conteúdos do tópico como fallback) —</option>
                                    @foreach($conteudos->where('topic_id', $quiz->topic_id) as $c)
                                        <option value="{{ $c->id }}">{{ $c->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label style="font-size:0.85rem; font-weight:500; color:var(--text-secondary); display:block; margin-bottom:0.6rem;">Alternativas * <span style="font-weight:400; color:var(--text-muted)">(clique em ✓ para marcar a correta)</span></label>
                            <div id="alts-{{ $quiz->id }}" style="display:flex; flex-direction:column; gap:0.5rem;">
                                @for($a = 0; $a < 4; $a++)
                                    <div style="display:flex; gap:0.5rem; align-items:center;">
                                        <button type="button" onclick="setCorreta('{{ $quiz->id }}', {{ $a }}, this)"
                                            style="width:34px; height:34px; border-radius:50%; border:2px solid var(--border-color); background:transparent; color:var(--text-secondary); cursor:pointer; flex-shrink:0; font-size:0.85rem; transition:all 0.2s;" title="Marcar como correta">
                                            {{ $a+1 }}
                                        </button>
                                        <input type="text" name="alternativas[{{ $a }}][descricao]" class="form-control" style="padding:0.6rem 0.9rem; font-size:0.9rem;" placeholder="Alternativa {{ chr(65+$a) }}" required>
                                    </div>
                                @endfor
                            </div>

                            <button type="submit" class="btn btn-primary" style="margin-top:1rem; width:100%;">
                                <i class="fa-solid fa-plus"></i> Adicionar Questão ao Quiz
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center; padding:3rem; color:var(--text-secondary);">
            <i class="fa-solid fa-circle-question" style="font-size:2.5rem; display:block; margin-bottom:1rem; opacity:0.4;"></i>
            Nenhum quiz cadastrado ainda. Clique em <strong>Novo Quiz</strong> para começar.
        </div>
    @endforelse
</div>

{{-- Modal: Novo Quiz --}}
<div id="modal-novo-quiz" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:480px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-plus" style="color:var(--primary);"></i> Novo Quiz</h3>
            <button onclick="document.getElementById('modal-novo-quiz').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form action="{{ route('admin.quizzes.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Título *</label>
                <input type="text" name="titulo" class="form-control" style="padding:0.8rem 1rem;" placeholder="Ex: Quiz de Banco de Dados — Nível 1" required>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Tópico *</label>
                <select name="topic_id" class="form-control" style="padding:0.8rem 1rem;" required>
                    <option value="">Selecione um tópico...</option>
                    @foreach($topicos as $t)
                        <option value="{{ $t->id }}">{{ $t->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" class="form-control" style="padding:0.8rem 1rem; min-height:80px; resize:vertical;" placeholder="Descrição opcional..."></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-novo-quiz').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Criar Quiz</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Quiz --}}
<div id="modal-editar-quiz" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:480px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-pen" style="color:var(--primary);"></i> Editar Quiz</h3>
            <button onclick="document.getElementById('modal-editar-quiz').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form id="form-editar-quiz" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Título *</label>
                <input type="text" name="titulo" id="edit-quiz-titulo" class="form-control" style="padding:0.8rem 1rem;" required>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Tópico *</label>
                <select name="topic_id" id="edit-quiz-topic" class="form-control" style="padding:0.8rem 1rem;" required>
                    @foreach($topicos as $t)
                        <option value="{{ $t->id }}">{{ $t->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" id="edit-quiz-desc" class="form-control" style="padding:0.8rem 1rem; min-height:80px; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-editar-quiz').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleAccordion(id) {
    const body = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    const isOpen = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'block';
    icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function openEditQuiz(id, titulo, descricao, topicId) {
    document.getElementById('edit-quiz-titulo').value = titulo;
    document.getElementById('edit-quiz-desc').value   = descricao;
    document.getElementById('edit-quiz-topic').value  = topicId;
    document.getElementById('form-editar-quiz').action = '/admin/quizzes/' + id;
    document.getElementById('modal-editar-quiz').style.display = 'flex';
}

function setCorreta(quizId, index, btn) {
    document.getElementById('correta-' + quizId).value = index;
    // Reset all buttons
    const container = document.getElementById('alts-' + quizId);
    container.querySelectorAll('button').forEach((b, i) => {
        b.style.background    = 'transparent';
        b.style.borderColor   = 'var(--border-color)';
        b.style.color         = 'var(--text-secondary)';
    });
    // Highlight selected
    btn.style.background  = 'var(--success)';
    btn.style.borderColor = 'var(--success)';
    btn.style.color       = '#fff';
}
</script>
@endsection

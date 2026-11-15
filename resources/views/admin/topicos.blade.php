@extends('layouts.app')

@section('title', 'Gerenciar Tópicos — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-book-bookmark" style="color:var(--primary);"></i> Gerenciar Tópicos
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Cadastre e gerencie os tópicos de conhecimento da plataforma.</p>
    </div>
    <div style="display:flex; gap:0.8rem;">
        <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <button onclick="document.getElementById('modal-novo-topico').style.display='flex'" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Novo Tópico
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background:var(--success-light); border:1px solid var(--success); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--success); display:flex; align-items:center; gap:0.6rem;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="grid-3">
    @forelse($topicos as $topico)
        <div class="card" style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:0.8rem;">
                    <div class="topic-icon"><i class="fa-solid fa-book"></i></div>
                    <h3 style="font-size:1.1rem; font-weight:600;">{{ $topico->nome }}</h3>
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <button onclick="openEditTopico({{ $topico->id }}, '{{ addslashes($topico->nome) }}', '{{ addslashes($topico->descricao ?? '') }}')" style="background:var(--primary-light); color:var(--primary); border:none; border-radius:8px; padding:0.4rem 0.6rem; cursor:pointer;">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <form action="{{ route('admin.topicos.destroy', $topico) }}" method="POST" onsubmit="return confirm('Excluir tópico {{ addslashes($topico->nome) }}? Isso removerá todos os dados relacionados.');">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:var(--danger-light); color:var(--danger); border:none; border-radius:8px; padding:0.4rem 0.6rem; cursor:pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <p style="font-size:0.85rem; color:var(--text-secondary); flex:1;">{{ $topico->descricao ?? 'Sem descrição.' }}</p>
            <div style="display:flex; gap:1rem; font-size:0.8rem; color:var(--text-secondary);">
                <span><i class="fa-solid fa-file-lines"></i> {{ $topico->contents_count }} conteúdos</span>
                <span><i class="fa-solid fa-circle-question"></i> {{ $topico->quizzes_count }} quizzes</span>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-secondary);">
            <i class="fa-solid fa-book-open" style="font-size:2.5rem; margin-bottom:1rem; opacity:0.4; display:block;"></i>
            Nenhum tópico cadastrado ainda. Clique em <strong>Novo Tópico</strong> para começar.
        </div>
    @endforelse
</div>

{{-- Modal: Novo Tópico --}}
<div id="modal-novo-topico" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:480px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-plus" style="color:var(--primary);"></i> Novo Tópico</h3>
            <button onclick="document.getElementById('modal-novo-topico').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form action="{{ route('admin.topicos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Nome do Tópico *</label>
                <input type="text" name="nome" class="form-control" style="padding:0.8rem 1rem;" placeholder="Ex: Banco de Dados" required>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" class="form-control" style="padding:0.8rem 1rem; min-height:100px; resize:vertical;" placeholder="Descrição opcional do tópico..."></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-novo-topico').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Criar Tópico</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Tópico --}}
<div id="modal-editar-topico" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:480px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-pen" style="color:var(--primary);"></i> Editar Tópico</h3>
            <button onclick="document.getElementById('modal-editar-topico').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form id="form-editar-topico" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Nome do Tópico *</label>
                <input type="text" name="nome" id="edit-topico-nome" class="form-control" style="padding:0.8rem 1rem;" required>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" id="edit-topico-desc" class="form-control" style="padding:0.8rem 1rem; min-height:100px; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-editar-topico').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openEditTopico(id, nome, descricao) {
    document.getElementById('edit-topico-nome').value = nome;
    document.getElementById('edit-topico-desc').value = descricao;
    document.getElementById('form-editar-topico').action = '/admin/topicos/' + id;
    document.getElementById('modal-editar-topico').style.display = 'flex';
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'Gerenciar Conteúdos — Estudaí')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-file-lines" style="color:var(--primary);"></i> Gerenciar Conteúdos
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Cadastre artigos, vídeos e materiais de estudo para cada tópico.</p>
    </div>
    <div style="display:flex; gap:0.8rem;">
        <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <button onclick="document.getElementById('modal-novo-conteudo').style.display='flex'" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Novo Conteúdo
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background:var(--success-light); border:1px solid var(--success); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--success); display:flex; align-items:center; gap:0.6rem;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Título</th>
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Tópico</th>
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">URL</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conteudos as $conteudo)
                    <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:1rem 1.5rem;">
                            <div style="font-weight:600;">{{ $conteudo->titulo }}</div>
                            @if($conteudo->descricao)
                                <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:0.2rem;">{{ Str::limit($conteudo->descricao, 80) }}</div>
                            @endif
                        </td>
                        <td style="padding:1rem 1.5rem;">
                            <span style="background:var(--primary-light); color:var(--primary); border-radius:20px; padding:0.3rem 0.8rem; font-size:0.8rem; font-weight:600;">{{ $conteudo->topic->nome }}</span>
                        </td>
                        <td style="padding:1rem 1.5rem;">
                            @if($conteudo->url)
                                <a href="{{ $conteudo->url }}" target="_blank" style="color:var(--primary); font-size:0.85rem;">{{ Str::limit($conteudo->url, 45) }} <i class="fa-solid fa-external-link"></i></a>
                            @else
                                <span style="color:var(--text-muted); font-size:0.85rem;">—</span>
                            @endif
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center;">
                            <div style="display:flex; gap:0.5rem; justify-content:center;">
                                <button onclick="openEditConteudo({{ $conteudo->id }}, '{{ addslashes($conteudo->titulo) }}', '{{ addslashes($conteudo->descricao ?? '') }}', '{{ addslashes($conteudo->url ?? '') }}', {{ $conteudo->topic_id }})"
                                    style="background:var(--primary-light); color:var(--primary); border:none; border-radius:8px; padding:0.4rem 0.7rem; cursor:pointer;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.conteudos.destroy', $conteudo) }}" method="POST" onsubmit="return confirm('Excluir este conteúdo?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:var(--danger-light); color:var(--danger); border:none; border-radius:8px; padding:0.4rem 0.7rem; cursor:pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:3rem; text-align:center; color:var(--text-secondary);">
                            <i class="fa-solid fa-file-circle-plus" style="font-size:2.5rem; display:block; margin-bottom:1rem; opacity:0.4;"></i>
                            Nenhum conteúdo cadastrado. Clique em <strong>Novo Conteúdo</strong> para começar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: Novo Conteúdo --}}
<div id="modal-novo-conteudo" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:540px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-plus" style="color:var(--primary);"></i> Novo Conteúdo</h3>
            <button onclick="document.getElementById('modal-novo-conteudo').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form action="{{ route('admin.conteudos.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Título *</label>
                <input type="text" name="titulo" class="form-control" style="padding:0.8rem 1rem;" placeholder="Título do conteúdo" required>
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
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">URL do conteúdo</label>
                <input type="url" name="url" class="form-control" style="padding:0.8rem 1rem;" placeholder="https://...">
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" class="form-control" style="padding:0.8rem 1rem; min-height:90px; resize:vertical;" placeholder="Descrição do conteúdo..."></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end; margin-top:1rem;">
                <button type="button" onclick="document.getElementById('modal-novo-conteudo').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Cadastrar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Conteúdo --}}
<div id="modal-editar-conteudo" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:2rem; width:100%; max-width:540px; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-weight:700;"><i class="fa-solid fa-pen" style="color:var(--primary);"></i> Editar Conteúdo</h3>
            <button onclick="document.getElementById('modal-editar-conteudo').style.display='none'" style="background:none; border:none; color:var(--text-secondary); font-size:1.4rem; cursor:pointer;">×</button>
        </div>
        <form id="form-editar-conteudo" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Título *</label>
                <input type="text" name="titulo" id="edit-cont-titulo" class="form-control" style="padding:0.8rem 1rem;" required>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Tópico *</label>
                <select name="topic_id" id="edit-cont-topic" class="form-control" style="padding:0.8rem 1rem;" required>
                    @foreach($topicos as $t)
                        <option value="{{ $t->id }}">{{ $t->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">URL</label>
                <input type="url" name="url" id="edit-cont-url" class="form-control" style="padding:0.8rem 1rem;">
            </div>
            <div class="form-group">
                <label style="font-size:0.9rem; font-weight:500; color:var(--text-secondary);">Descrição</label>
                <textarea name="descricao" id="edit-cont-desc" class="form-control" style="padding:0.8rem 1rem; min-height:90px; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; gap:0.8rem; justify-content:flex-end; margin-top:1rem;">
                <button type="button" onclick="document.getElementById('modal-editar-conteudo').style.display='none'" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openEditConteudo(id, titulo, descricao, url, topicId) {
    document.getElementById('edit-cont-titulo').value = titulo;
    document.getElementById('edit-cont-desc').value   = descricao;
    document.getElementById('edit-cont-url').value    = url;
    document.getElementById('edit-cont-topic').value  = topicId;
    document.getElementById('form-editar-conteudo').action = '/admin/conteudos/' + id;
    document.getElementById('modal-editar-conteudo').style.display = 'flex';
}
</script>
@endsection

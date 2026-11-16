@extends('layouts.app')

@section('title', 'Editar Usuário — Estudaí')

@section('content')
<div style="max-width:600px; margin:0 auto;">
    <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1.5rem; font-size:0.85rem; color:var(--text-secondary);">
        <a href="{{ route('admin.usuarios') }}" style="color:var(--text-secondary); text-decoration:none;">Usuários</a>
        <i class="fa-solid fa-chevron-right" style="font-size:0.65rem;"></i>
        <span style="color:var(--text-primary); font-weight:600;">Editar</span>
    </div>

    <div class="card">
        <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-user-pen" style="color:var(--primary);"></i> Editar Usuário
        </h2>

        @if($errors->any())
            <div style="background:var(--danger-light); border:1px solid var(--danger); border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; color:var(--danger);">
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.usuarios.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nome completo</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Perfil</label>
                <select name="tipo" class="form-control">
                    <option value="estudante" {{ $user->tipo === 'estudante' ? 'selected' : '' }}>Estudante</option>
                    <option value="administrador" {{ $user->tipo === 'administrador' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nova senha <span style="color:var(--text-muted); font-size:0.8rem;">(deixe em branco para manter a atual)</span></label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar nova senha</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div style="display:flex; gap:0.8rem; justify-content:flex-end; margin-top:2rem;">
                <a href="{{ route('admin.usuarios') }}" class="btn" style="background:var(--bg-primary); border:1px solid var(--border-color); color:var(--text-primary);">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

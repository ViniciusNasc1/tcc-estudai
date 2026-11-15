@extends('layouts.app')

@section('title', 'Gerenciar Usuários — Estudaí')

@section('content')
{{-- ─── Header ──────────────────────────────────────────────────────────────── --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <div>
        <h2 style="font-size:1.6rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-users" style="color:var(--primary);"></i> Gerenciar Usuários
        </h2>
        <p style="color:var(--text-secondary); font-size:0.9rem;">Visualize e gerencie todos os usuários cadastrados na plataforma.</p>
    </div>
    <a href="{{ route('admin.home') }}" class="btn" style="background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary);">
        <i class="fa-solid fa-arrow-left"></i> Voltar
    </a>
</div>

{{-- ─── Flash Messages ──────────────────────────────────────────────────────── --}}
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

{{-- ─── Tabela de Usuários ─────────────────────────────────────────────────── --}}
<div class="card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-primary);">
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Usuário</th>
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">E-mail</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Perfil</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Quizzes</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Média</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Cadastro</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    @php
                        $media = round($usuario->media_desempenho);
                        $badgeClass = $media >= 80 ? 'badge-dominado' : ($media >= 50 ? 'badge-revisao' : 'badge-prioritario');
                        $names = explode(' ', $usuario->name);
                        $initials = count($names) >= 2
                            ? strtoupper(substr($names[0],0,1).substr($names[count($names)-1],0,1))
                            : strtoupper(substr($names[0],0,2));
                    @endphp
                    <tr style="border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='rgba(94,92,230,0.05)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:1rem 1.5rem;">
                            <div style="display:flex; align-items:center; gap:0.8rem;">
                                <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#a855f7); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; color:#fff; flex-shrink:0;">{{ $initials }}</div>
                                <span style="font-weight:600;">{{ $usuario->name }}</span>
                            </div>
                        </td>
                        <td style="padding:1rem 1.5rem; color:var(--text-secondary); font-size:0.9rem;">{{ $usuario->email }}</td>
                        <td style="padding:1rem 1.5rem; text-align:center;">
                            @if($usuario->tipo === 'administrador')
                                <span style="background:var(--primary-light); color:var(--primary); border-radius:20px; padding:0.3rem 0.8rem; font-size:0.75rem; font-weight:700;">Admin</span>
                            @else
                                <span style="background:rgba(168,85,247,0.1); color:#a855f7; border-radius:20px; padding:0.3rem 0.8rem; font-size:0.75rem; font-weight:700;">Estudante</span>
                            @endif
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center; font-weight:600;">{{ $usuario->total_quizzes }}</td>
                        <td style="padding:1rem 1.5rem; text-align:center;">
                            @if($usuario->total_quizzes > 0)
                                <span class="rec-badge {{ $badgeClass }}">{{ $media }}%</span>
                            @else
                                <span style="color:var(--text-secondary);">—</span>
                            @endif
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td style="padding:1rem 1.5rem; text-align:center;">
                            @if($usuario->tipo !== 'administrador')
                                <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o usuário {{ addslashes($usuario->name) }}? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:var(--danger-light); color:var(--danger); border:1px solid var(--danger); border-radius:8px; padding:0.4rem 0.8rem; cursor:pointer; font-size:0.8rem; font-weight:600; transition:all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='#fff';" onmouseout="this.style.background='var(--danger-light)'; this.style.color='var(--danger)';">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </button>
                                </form>
                            @else
                                <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:3rem; text-align:center; color:var(--text-secondary);">
                            <i class="fa-solid fa-users-slash" style="font-size:2rem; margin-bottom:1rem; display:block; opacity:0.4;"></i>
                            Nenhum usuário cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

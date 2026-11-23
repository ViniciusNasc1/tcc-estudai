<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Estudaí - Plataforma Inteligente')</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    <div class="app-container">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <div class="navbar-content">
                {{-- Logo / Brand --}}
                @auth
                    @if(auth()->user()->tipo === 'administrador')
                        <a href="{{ route('admin.home') }}" class="navbar-brand">
                    @else
                        <a href="{{ route('home') }}" class="navbar-brand">
                    @endif
                @else
                    <a href="{{ route('login') }}" class="navbar-brand">
                @endauth
                    <div class="logo-icon">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span>Estudaí</span>
                    @auth
                        @if(auth()->user()->tipo === 'administrador')
                            <span class="badge-admin">Admin</span>
                        @endif
                    @endauth
                </a>

                @auth
                <ul class="navbar-nav">
                    @if(auth()->user()->tipo === 'administrador')
                        {{-- Menu do Administrador --}}
                        <li>
                            <a href="{{ route('admin.home') }}" class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                                <i class="fa-solid fa-gauge-high"></i> Painel
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.usuarios') }}" class="nav-link {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Usuários
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.topicos') }}" class="nav-link {{ request()->routeIs('admin.topicos*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-bookmark"></i> Tópicos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.conteudos') }}" class="nav-link {{ request()->routeIs('admin.conteudos*') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-lines"></i> Conteúdos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.quizzes') }}" class="nav-link {{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}">
                                <i class="fa-solid fa-circle-question"></i> Quizzes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.relatorios') }}" class="nav-link {{ request()->routeIs('admin.relatorios') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-pie"></i> Relatórios
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.historico') }}" class="nav-link {{ request()->routeIs('admin.historico') ? 'active' : '' }}">
                                <i class="fa-solid fa-timeline"></i> Histórico
                            </a>
                        </li>
                    @else
                        {{-- Menu do Estudante --}}
                        <li>
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="fa-solid fa-house"></i> Início
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('topicos') }}" class="nav-link {{ request()->routeIs('topicos*') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open"></i> Tópicos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-simple"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recomendacoes') }}" class="nav-link {{ request()->routeIs('recomendacoes') ? 'active' : '' }}">
                                <i class="fa-solid fa-lightbulb"></i> Recomendações
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('historico.quizzes') }}" class="nav-link {{ request()->routeIs('historico.quizzes') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left"></i> Histórico
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('assistant') }}" class="nav-link {{ request()->routeIs('assistant') ? 'active' : '' }}">
                                <i class="fa-solid fa-robot"></i> Assistente IA
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('trilha') }}" class="nav-link {{ request()->routeIs('trilha') ? 'active' : '' }}">
                                <i class="fa-solid fa-route"></i> Trilha
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="navbar-actions">
                    <!-- User Initials -->
                    @php
                        $names = explode(' ', auth()->user()->name);
                        $initials = '';
                        if (count($names) >= 2) {
                            $initials = strtoupper(substr($names[0], 0, 1) . substr($names[count($names)-1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($names[0], 0, 2));
                        }
                    @endphp
                    <div class="user-profile-btn" title="{{ auth()->user()->name }}">
                        {{ $initials }}
                    </div>
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-link" title="Sair">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </nav>

        <!-- Main Body Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>

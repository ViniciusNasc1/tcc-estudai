@extends('layouts.app')

@section('title', 'Acessar o Estudaí')

@section('content')
<!-- We override the main-content padding since Auth has a custom wrapper -->
<style>
    .main-content {
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
    }
    .navbar {
        display: none !important; /* Hide navbar on auth screens */
    }
</style>

<div class="auth-wrapper">
    <!-- Left Sidebar Details -->
    <div class="auth-sidebar">
        <div class="auth-sidebar-content">
            <div class="auth-logo">
                <div class="logo-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <span>Estudaí</span>
            </div>
            <p class="auth-subtitle">Plataforma inteligente de acompanhamento de estudos baseada em recomendações e análise de desempenho.</p>
            
            <div class="auth-features">
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="auth-feature-text">
                        <h4>Desempenho em Tempo Real</h4>
                        <p>Visualize sua evolução por tópicos por meio de gráficos dinâmicos de barras e linhas.</p>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div class="auth-feature-text">
                        <h4>Recomendações Adaptativas</h4>
                        <p>O algoritmo inteligente gera sugestões de revisão com base nos seus erros e acertos em testes.</p>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div class="auth-feature-text">
                        <h4>Quizzes Inteligentes</h4>
                        <p>Responda a questionários e teste seu nível atual nas principais áreas de conhecimento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Auth Forms -->
    <div class="auth-panel">
        <div class="auth-card">
            <!-- Tabs Toggle -->
            <div class="auth-tabs">
                <button class="auth-tab active" id="tab-login" onclick="switchTab('login')">Entrar</button>
                <button class="auth-tab" id="tab-register" onclick="switchTab('register')">Criar conta</button>
            </div>

            <!-- Login Form -->
            <div class="auth-form active" id="form-login">
                <h3 class="form-title">Bem-vindo de volta</h3>
                <p class="form-subtitle">Acesse sua conta para continuar estudando.</p>
                
                <!-- Session Status (Breeze) -->
                @if (session('status'))
                    <div style="background-color: var(--success-light); color: var(--success); padding: 0.8rem; border-radius: var(--border-radius); font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid var(--success);">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('email'))
                    <div style="background-color: var(--danger-light); color: var(--danger); padding: 0.8rem; border-radius: var(--border-radius); font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid var(--danger);">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="login-email">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" id="login-email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required autocomplete="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Senha</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="login-password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        </div>
                    </div>

                    <div class="form-footer-links">
                        <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-secondary);">
                            <input type="checkbox" name="remember" style="accent-color: var(--primary);"> Lembrar-me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="form-link">Esqueceu a senha?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Entrar <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-form" id="form-register">
                <h3 class="form-title">Comece agora</h3>
                <p class="form-subtitle">Crie sua conta de estudante gratuita.</p>

                @if ($errors->any() && !$errors->has('email') || ($errors->any() && request()->is('register')))
                    <div style="background-color: var(--danger-light); color: var(--danger); padding: 0.8rem; border-radius: var(--border-radius); font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid var(--danger);">
                        <ul style="list-style-type: none; margin: 0; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reg-name">Nome Completo</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" name="name" id="reg-name" class="form-control" placeholder="Seu nome" value="{{ old('name') }}" required autocomplete="name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reg-email">E-mail</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" id="reg-email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reg-password">Senha (mín. 8 caracteres)</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="reg-password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reg-password-conf">Confirmar Senha</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password_confirmation" id="reg-password-conf" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                        Cadastrar <i class="fa-solid fa-user-plus"></i>
                    </button>
                </form>
            </div>
            
            <!-- Quick Login Tip for Presentation -->
            <div style="margin-top: 2rem; background-color: var(--primary-light); border: 1px dashed var(--primary); padding: 0.8rem; border-radius: var(--border-radius); font-size: 0.85rem; text-align: center; color: var(--text-secondary);">
                <i class="fa-solid fa-lightbulb" style="color: var(--primary);"></i> <strong>Acesso Rápido (TCC):</strong><br>
                <span style="display:inline-flex; gap:1.5rem; margin-top:0.4rem; flex-wrap:wrap; justify-content:center;">
                    <span>
                        <i class="fa-solid fa-user-graduate" style="color:var(--primary);"></i>
                        <strong>Estudante:</strong> <code>vinicius@email.com</code> / <code>senha123</code>
                    </span>
                    <span>
                        <i class="fa-solid fa-shield-halved" style="color:#a855f7;"></i>
                        <strong>Admin:</strong> <code>admin@email.com</code> / <code>senha123</code>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tab) {
        // Toggle tab headers
        document.getElementById('tab-login').classList.remove('active');
        document.getElementById('tab-register').classList.remove('active');
        document.getElementById('tab-' + tab).classList.add('active');

        // Toggle forms
        document.getElementById('form-login').classList.remove('active');
        document.getElementById('form-register').classList.remove('active');
        document.getElementById('form-' + tab).classList.add('active');
    }

    // Auto-switch to register if there are registration errors and not email error (login)
    @if ($errors->any() && !$errors->has('email'))
        switchTab('register');
    @endif
</script>
@endsection

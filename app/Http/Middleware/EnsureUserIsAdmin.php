<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Verifica se o utilizador autenticado é um administrador.
     * Caso contrário, redireciona para a home do estudante com uma mensagem de erro.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->tipo !== 'administrador') {
            return redirect()->route('home')->with('error', 'Acesso restrito a administradores.');
        }

        return $next($request);
    }
}

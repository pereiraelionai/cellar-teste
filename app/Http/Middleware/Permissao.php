<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Permissao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $controller, $criar_editar=false, $excluir=false): Response
    {   
        // Validar se tem permissão para acessar o controllador usuario
        if($controller == 'usuario') {
            if(!Auth::user()->admin) abort(403);
        }

        if($controller == 'categoria') {
            if(!session('permissao')->categorias) abort(403);
        }

        if($controller == 'produto') {
            if(!session('permissao')->produtos) abort(403);
        }
        
        if($controller != 'usuario' && boolval($criar_editar)) {
            if(!session('permissao')->criar_editar) return response()->json(['message' => 'Acesso negado'], 403);
        }

        if($controller != 'usuario' && boolval($excluir)) {
            if(!session('permissao')->excluir) return response()->json(['message' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}

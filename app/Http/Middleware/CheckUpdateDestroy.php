<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\User;

class CheckUpdateDestroy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    /**
     * Este middleware é aplicado em rotas que lidam com operações de atualização, exclusão ou ativação de recursos (categorias, produtos ou usuários) no sistema.
     * Ele verifica se o ID especificado na solicitação pertence ao usuário autenticado ou ao seu grupo de usuarios. Se o ID não pertencer a nenhum desses usuários, a solicitação será negada com um status 403 (Não autorizado).
     * 
     * O grupo de usuário inclui o usuário administrador que criou o usuário autenticado e todos os usuários criados por esse usuário administrador.
     */

    public function handle(Request $request, Closure $next, $controller): Response
    {   
        // CATEGORIA
        //------------------------------------------------------------------------------------
        if($controller == 'categoria') {
            $id = $request->route()->parameter('categoria');

            $categoria = Categoria::with('usuarios')
                                    ->where('categorias.id', $id)
                                    ->where(function ($query) {
                                        $query->where(function ($query) {
                                                $query->where('categorias.usuario_id', Auth::user()->created_by)
                                                    ->orWhere('categorias.usuario_id', Auth::user()->id);
                                            })
                                            ->orWhereHas('usuarios', function ($query) {
                                                $query->where('created_by', Auth::user()->id)
                                                    ->orWhere('created_by', Auth::user()->created_by);
                                            });
                                    })
                                    ->first();

            if(!$categoria) return response()->json(['message' => 'Não autorizado'], 403);
        }

        // PRODUTO
        //------------------------------------------------------------------------------------
        if($controller == 'produto') {
            $id = $request->route()->parameter('produto');

            $produto = Produto::with('categorias', 'usuarios')
                                ->where('produtos.id', $id)
                                ->where(function ($query) {
                                    $query->where(function ($query) {
                                            $query->where('produtos.usuario_id', Auth::user()->id)
                                                ->orWhere('produtos.usuario_id', Auth::user()->created_by);
                                        })
                                        ->orWhereHas('usuarios', function ($query) {
                                            $query->where('created_by', Auth::user()->id)
                                                ->orWhere('created_by', Auth::user()->created_by);
                                        });
                                })
                                ->first();

            if(!$produto) return response()->json(['message' => 'Não autorizado.'], 403); 
        }

        // USUARIO
        //------------------------------------------------------------------------------------
        if($controller == 'usuario') {
            $id = $request->route()->parameter('usuario');

            $usuario = User::withTrashed();
            if(Auth::user()->admin) {
                $usuario->find(Auth::user()->id);
            } else {
                $usuario->where('id', $id)->where('created_by', Auth::user()->id)->first();
            }
            
            if(!$usuario) return response()->json(['message' => 'Não autorizado.'], 403); 
        }


        return $next($request);
    }
}

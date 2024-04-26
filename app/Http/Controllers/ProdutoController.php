<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // Get no usuário
        $usuario = Auth::user();
        
        // Get nas categorias criadas pelo usuário e criadas pelos usuários do seu user admin
        $produtos = Produto::select(
                                    'produtos.id',
                                    'produtos.nome',
                                    'valor',
                                    'categorias.nome as categoria',
                                    'users.name as nome_usuario',
                                    'produtos.created_at'
                                )
                                ->leftJoin('users', 'users.id', '=', 'produtos.usuario_id')
                                ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
                                ->where('users.created_by', $usuario->created_by)
                                ->where('produtos.usuario_id', $usuario->id)
                                ->paginate(10);
        
        return view('produto.index', ['produtos' => $produtos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

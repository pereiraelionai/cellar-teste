<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

class CategoriaController extends Controller
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
        // Get nas categorias criadas pelo usuário e criadas pelos usuários do seu user admin
        $categorias = $this->getCategorias();
        
        return view('categoria.index', ['categorias' => $categorias]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {          
        // Validações
        $regras = [
            'nome' => [
                'required',
                'min:2',
                'max:30',
                Rule::unique('categorias')->where(function ($query) use ($request) {
                     // Verifica se o usuario ou o usuario admin ja cadastrou a categoria
                    return $query->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->whereNull('deleted_at');
                }),
            ]
        ];

        $msg = [
            'required' => 'O nome da catgoria é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Categoria já cadastrada'
        ];

        $request->validate($regras, $msg);

        // Salvando categoria
        $categoria = new Categoria();
        $categoria->nome = ucfirst($request->input('nome'));
        $categoria->usuario_id = Auth::user()->id;
        $categoria->save();

        // Tratando erro
        if(!$categoria) return response()->json(['message' => 'Erro ao cadastrar categoria', 500]);

        // Get nas categorias
        $categorias = $this->getCategorias();

        // Montando tabela de categorias
        $view = View::make('categoria/table', ['categorias' => $categorias])->render();                                

        return response()->json($view, 200);

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
        $categoria = Categoria::where('id', $id)->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->first();
        if(!$categoria) return response()->json(['message' => 'Categoria não encontrada.'], 404); // Verifica se existe a categoria para esse usuario

        if(!$categoria->delete()) return response()->json(['message' => 'Erro ao excluir categoria.'], 500);

        // Get nas categorias
        $categorias = $this->getCategorias();

        // Montando tabela de categorias
        $view = View::make('categoria/table', ['categorias' => $categorias])->render();  

        return response()->json($view, 200);

    }

    private function getCategorias()
    {
        return Categoria::select(
            'categorias.id',
            'nome',
            'users.name as nome_usuario',
            'categorias.created_at'
        )
        ->leftJoin('users', 'users.id', '=', 'categorias.usuario_id')
        ->where('users.created_by', Auth::user()->created_by)
        ->orWhere('categorias.usuario_id', Auth::user()->id)
        ->orderByDesc('id')
        ->paginate(10);
    }
}

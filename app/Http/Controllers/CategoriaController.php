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
        $categorias = self::getCategorias();
        
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
            'unique' => 'Categoria já cadastrada.'
        ];

        $request->validate($regras, $msg);

        // Salvando categoria
        $categoria = new Categoria();
        $categoria->nome = ucfirst($request->input('nome'));
        $categoria->usuario_id = Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id;
        $categoria->save();

        if(!$categoria) return response()->json(['message' => 'Erro ao cadastrar categoria', 500]);

        $categorias = self::getCategorias();

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();                                

        return response()->json($view, 201);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // Verifica se a categoria pertence ao usuario ou ao usuario admin
        $categoria = Categoria::where('id', $id)->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->first();
        if(!$categoria) return response()->json(['message' => 'Não autorizado'], 403);
        
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
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Categoria já cadastrada.'
        ];

        $request->validate($regras, $msg);

        // Editando a categoria
        $updatedSuccess = $categoria->update([
            'nome' => ucfirst($request->input('nome'))
        ]);

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar categoria', 500]);

        $categorias = self::getCategorias();

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();                                

        return response()->json($view, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $categoria = Categoria::where('id', $id)->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->first();
        if(!$categoria) return response()->json(['message' => 'Não autorizado.'], 403); // Verifica se existe a categoria para esse usuario ou usuario admin

        if(!$categoria->delete()) return response()->json(['message' => 'Erro ao excluir categoria.'], 500);

        $categorias = self::getCategorias();

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();  

        return response()->json($view, 200);

    }

    public static function getCategorias()
    {
        return Categoria::with('usuarios')
                        ->orWhere('categorias.usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)
                        ->orderByDesc('id')
                        ->paginate(10);
    }
}

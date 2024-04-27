<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CategoriaController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $categorias = self::getCategorias()->paginate(10);
        
        return view('categoria.index', ['categorias' => $categorias]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {          
        // Validações
        $regras = [
            'nome' => 'required|min:2|max:30|categoria' // A validação categoria verifica se a categoria já nao foi criada por algum usuario relacionado
        ];

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'categoria' => 'Categoria já cadastrada.'
        ];

        $request->validate($regras, $msg);

        // Salvando categoria
        $categoria = new Categoria();
        $categoria->nome = ucfirst($request->input('nome'));
        $categoria->usuario_id = Auth::user()->id;
        $categoria->save();

        if(!$categoria->id) return response()->json(['message' => 'Erro ao cadastrar categoria'], 500);

        $categorias = self::getCategorias()->paginate(10);

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();                                

        return response()->json($view, 201);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {        
        // Validações
        $regras = [
            'nome' => 'required|min:2|max:30|categoria' // A validação categoria verifica se a categoria já nao foi criada por algum usuario relacionado
        ];

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'categoria' => 'Categoria já cadastrada.'
        ];

        $request->validate($regras, $msg);

        $categoria = Categoria::find($id);
        $updatedSuccess = $categoria->update([
            'nome' => ucfirst($request->input('nome'))
        ]);

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar categoria'], 500);

        $categorias = self::getCategorias()->paginate(10);

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();                                

        return response()->json($view, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $categoria = Categoria::find($id);
        if(!$categoria->delete()) return response()->json(['message' => 'Erro ao excluir categoria.'], 500);

        $categorias = self::getCategorias()->paginate(10);

        $view = View::make('categoria/table', ['categorias' => $categorias])->render();  

        return response()->json($view, 200);

    }

    public static function getCategorias()
    {
        return Categoria::with('usuarios')
                        ->where(function ($query) {
                            $query->where('categorias.usuario_id', Auth::user()->created_by)
                                  ->orWhere('categorias.usuario_id', Auth::user()->id);
                        })
                        ->orWhereHas('usuarios', function ($query) {
                            $query->where('created_by', Auth::user()->id)
                                ->orWhere('created_by', Auth::user()->created_by);
                        })
                        ->orderByDesc('id');
    }
}

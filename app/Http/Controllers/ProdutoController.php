<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ProdutoController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $categorias = CategoriaController::getCategorias()->get();
        $produtos = self::getProdutos()->paginate(10);
        
        return view('produto.index', ['produtos' => $produtos, 'categorias' => $categorias]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validações
        $regras = [
            'nome' => 'required|min:2|max:30|produto',  // A validação produto verifica se o produto já nao foi criada por algum usuario relacionado
            'valor' => 'required|valor',
            'categoria' => 'required|exists:categorias,id'
        ];

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'produto' => 'Produto já cadastrado.',
            'exists' => 'Categoria inexistente.',
            'valor' => 'Valor máximo R$ 999.999,99'
        ];

        $request->validate($regras, $msg);

        // Convertendo o valor em reais para float
        $valorString = $request->input('valor');
        $valorString = str_replace(['R$', ' '], '', $valorString);
        $valorFloat = (float) str_replace(',', '.', $valorString);

        // Cadastrando o produto
        $produto = new Produto();
        $produto->nome = ucfirst($request->input('nome'));
        $produto->valor = $valorFloat;
        $produto->categoria_id = $request->input('categoria');
        $produto->usuario_id = Auth::user()->id;
        $produto->save();

        if(!$produto->id) return response()->json(['message' => 'Erro ao cadastrar produto'], 500);

        $produtos = self::getProdutos()->paginate(10);

        $view = View::make('produto/table', ['produtos' => $produtos])->render();                                

        return response()->json($view, 201);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produto = Produto::find($id);

        // Validações
        $regras = [
            'valor' => 'required|valor',
            'categoria' => 'required|exists:categorias,id'
        ];
        
        if ($produto->nome != $request->input('nome')) {
            $regras['nome'] = 'required|min:2|max:30|produto'; // A validação produto verifica se o produto já nao foi criada por algum usuario relacionado
        } else {
            $regras['nome'] = 'required|min:2|max:30';
        }

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'produto' => 'Produto já cadastrado.',
            'exists' => 'Categoria inexistente.',
            'valor' => 'Valor máximo R$ 999.999,99'
        ];
        $request->validate($regras, $msg);

        // Convertendo o valor em reais para float
        $valorString = $request->input('valor');
        $valorString = str_replace(['R$', ' '], '', $valorString);
        $valorFloat = (float) str_replace(',', '.', $valorString);

        // Editando a categoria
        $updatedSuccess = $produto->update([
            'nome' => ucfirst($request->input('nome')),
            'valor' => $valorFloat,
            'categoria_id' => $request->input('categoria')
        ]);

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar produto'], 500);

        $produtos = self::getProdutos()->paginate(10);

        $view = View::make('produto/table', ['produtos' => $produtos])->render();                                

        return response()->json($view, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produto = Produto::find($id);
        if(!$produto->delete()) return response()->json(['message' => 'Erro ao excluir produto.'], 500);

        $produtos = self::getProdutos()->paginate(10);

        $view = View::make('produto/table', ['produtos' => $produtos])->render();  

        return response()->json($view, 200);
    }

    public static function getProdutos()
    {
        return Produto::with('categorias')
                      ->with('usuarios')
                      ->where(function ($query) {
                          $query->where('produtos.usuario_id', Auth::user()->id)
                                ->orWhere('produtos.usuario_id', Auth::user()->created_by);
                      })
                      ->orWhereHas('usuarios', function ($query) {
                          $query->where('created_by', Auth::user()->id)
                                ->orWhere('created_by', Auth::user()->created_by);
                      })
                      ->orderByDesc('id');
    }
}

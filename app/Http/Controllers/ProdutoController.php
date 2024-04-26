<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

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
        $categorias = CategoriaController::getCategorias();
        $produtos = $this->getProdutos();
        
        return view('produto.index', ['produtos' => $produtos, 'categorias' => $categorias]);
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
                Rule::unique('produtos')->where(function ($query) use ($request) {
                    // Verifica se o usuario ou o usuario admin ja cadastrou a categoria
                    return $query->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->whereNull('deleted_at');
                }),
            ],
            'valor' => 'required|valor',
            'categoria' => 'required|exists:categorias,id'
        ];

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Produto já cadastrado.',
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
        $produto->usuario_id = Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id;
        $produto->save();

        if(!$produto) return response()->json(['message' => 'Erro ao cadastrar produto', 500]);

        $produtos = $this->getProdutos();

        $view = View::make('produto/table', ['produtos' => $produtos])->render();                                

        return response()->json($view, 201);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verifica se o produto pertence ao usuario ou ao usuario admin
        $produto = Produto::where('id', $id)->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->first();
        if(!$produto) return response()->json(['message' => 'Não autorizado'], 403);

        // Validações
        if($produto->nome != $request->input('nome')) {
            $regras = [
                'nome' => [
                    'required',
                    'min:2',
                    'max:30',
                    Rule::unique('produtos')->where(function ($query) use ($request) {
                        // Verifica se o usuario ou o usuario admin ja cadastrou a categoria
                        return $query->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->whereNull('deleted_at');
                    }),
                ],
                'valor' => 'required|valor',
                'categoria' => 'required|exists:categorias,id'
            ];
        } else {
            $regras = [
                'nome' => 'required|min:2|max:30',
                'valor' => 'required|valor',
                'categoria' => 'required|exists:categorias,id'
            ];
        }

        $msg = [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Produto já cadastrado.',
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

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar produto', 500]);

        $produtos = $this->getProdutos();

        $view = View::make('produto/table', ['produtos' => $produtos])->render();                                

        return response()->json($view, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produto = Produto::where('id', $id)->where('usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)->first();
        if(!$produto) return response()->json(['message' => 'Não autorizado.'], 403); // Verifica se existe o produto para esse usuario ou usuario admin

        if(!$produto->delete()) return response()->json(['message' => 'Erro ao excluir produto.'], 500);

        $produtos = $this->getProdutos();

        $view = View::make('produto/table', ['produtos' => $produtos])->render();  

        return response()->json($view, 200);
    }

    private function getProdutos()
    {
        return Produto::with('categorias')
                        ->with('usuarios')
                        ->orWhere('produtos.usuario_id', Auth::user()->created_by ? Auth::user()->created_by : Auth::user()->id)
                        ->orderByDesc('id')
                        ->paginate(10);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Permissao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class UsuarioController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $usuarios = User::withTrashed()->with('permissao')->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        return view('usuario.index', ['usuarios' => $usuarios]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validações
        $regras = [
            'name' => 'required|min:3|max:30',
            'email' => 'required|email|unique:users',
            'permissoes'=> 'json'
        ];

        $msg = [
            'name.required' => 'O campo nome é obrigatório',
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'Email inválido',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Email já cadastrado.',
            'json' => 'Permissões com formato inválido.'
        ];

        $request->validate($regras, $msg);

        $permissoes = json_decode($request->input('permissoes'));

        $usuario = new User();
        $usuario->name = ucwords($request->input('name'));
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt('Cellar@123');
        $usuario->admin = false;
        $usuario->created_by = Auth::user()->id;
        $usuario->save();

        if(!$usuario->id) return response()->json(['message' => 'Erro ao cadastrar usuário'], 500);

        // Envia o email de verificação
        $usuario->sendEmailVerificationNotification();

        // Criando registro na tb permissoes
        $permissao = Permissao::create([
            'usuario_id' => $usuario->id,
            'categorias' => $permissoes->categoria,
            'produtos' => $permissoes->produto,
            'criar_editar' => $permissoes->criar_editar,
            'excluir' => $permissoes->excluir,
        ]);

        if(!$permissao->id) return response()->json(['message' => 'Erro ao cadastrar permissões'], 500);

        $usuarios = User::withTrashed()->with('permissao')->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();                                

        return response()->json($view, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validações
        $regras = [
            'name' => 'required|min:3|max:30',
            'permissoes' => 'json'
        ];
    
        $msg = [
            'name.required' => 'O campo nome é obrigatório',
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Email já cadastrado.',
            'json' => 'Permissões com formato inválido.'
        ];
    
        $request->validate($regras, $msg);

        $permissoes = json_decode($request->input('permissoes'));
        
        // Editando usuario
        $usuario = User::withTrashed()->find($id);
        $updatedSuccess = $usuario->update([
            'name' => ucfirst($request->input('name'))
        ]);

        // Editando as permissões se o usuario nao for admin
        if(!$usuario->admin) {
            $permissao = Permissao::where('usuario_id', $usuario->id)->first();
            if ($permissao) {
                // Atualiza os atributos do modelo
                $permissao->categorias = $permissoes->categoria;
                $permissao->produtos = $permissoes->produto;
                $permissao->criar_editar = $permissoes->criar_editar;
                $permissao->excluir = $permissoes->excluir;
    
                // Salva as alterações no banco de dados
                $permissao->save();
            } else {
                return response()->json(['message' => 'Erro ao editar permissões', 500]);
            }
        }

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar usuário', 500]);
    
        $usuarios = User::withTrashed()->with('permissao')->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);
        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();
    
        return response()->json($view, 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $usuario = User::withTrashed()->find($id);
        if(!$usuario->delete()) return response()->json(['message' => 'Erro ao inativar usuário.'], 500);

        $usuarios = User::withTrashed()->with('permissao')->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();  

        return response()->json($view, 200);
    }

    public function ativar(string $id)
    {   
        $usuario = User::withTrashed()->find($id);
        if (!$usuario->restore()) return response()->json(['message' => 'Erro ao ativar usuário.'], 500);

        $usuarios = User::withTrashed()->with('permissao')->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();  

        return response()->json($view, 200);
    }
}

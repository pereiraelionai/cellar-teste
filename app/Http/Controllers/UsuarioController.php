<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class UsuarioController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.updates:usuario')->only(['update', 'destroy', 'ativar']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $usuarios = User::withTrashed()->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

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
            'email' => 'required|email|unique:users'
        ];

        $msg = [
            'name.required' => 'O campo nome é obrigatório',
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'Email inválido',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Email já cadastrado.'
        ];

        $request->validate($regras, $msg);

        $usuario = new User();
        $usuario->name = ucwords($request->input('name'));
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt('Cellar@123');
        $usuario->admin = false;
        $usuario->created_by = Auth::user()->id;
        $usuario->save();

        if(!$usuario->id) return response()->json(['message' => 'Erro ao cadastrar usuário'], 500);

        $usuarios = User::withTrashed()->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

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
        ];
    
        $msg = [
            'name.required' => 'O campo nome é obrigatório',
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'Digite pelo menos 2 caracteres.',
            'max' => 'Digite no máximo 30 caracteres.',
            'unique' => 'Email já cadastrado.'
        ];
    
        $request->validate($regras, $msg);
        
        // Editando usuario
        $usuario = User::withTrashed()->find($id);
        $updatedSuccess = $usuario->update([
            'name' => ucfirst($request->input('name'))
        ]);

        if(!$updatedSuccess) return response()->json(['message' => 'Erro ao editar usuário', 500]);
    
        $usuarios = User::withTrashed()->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);
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

        $usuarios = User::withTrashed()->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();  

        return response()->json($view, 200);
    }

    public function ativar(string $id)
    {   
        $usuario = User::withTrashed()->find($id);
        if (!$usuario->restore()) return response()->json(['message' => 'Erro ao ativar usuário.'], 500);

        $usuarios = User::withTrashed()->where('id', Auth::user()->id)->orWhere('created_by', Auth::user()->id)->paginate(10);

        $view = View::make('usuario/table', ['usuarios' => $usuarios])->render();  

        return response()->json($view, 200);
    }
}

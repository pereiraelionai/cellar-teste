<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categorias = CategoriaController::getCategorias()->count();
        $produtos = ProdutoController::getProdutos()->count();
        $usuarios = UsuarioController::getUsuarios()->count();

        $data = array();
        $data['categorias'] = $categorias;
        $data['produtos'] = $produtos;
        $data['usuarios'] = $usuarios;

        return view('home', ['data' => $data]);
    }
}

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/categoria', [CategoriaController::class, 'index'])->name('categoria.index')->middleware('permissao:categoria');
    Route::post('/categoria', [CategoriaController::class, 'store'])->name('categoria.store')->middleware('permissao:categoria');
    Route::put('/categoria/{categoria}', [CategoriaController::class, 'update'])->name('categoria.update')->middleware('check.updates:categoria')->middleware('permissao:categoria,true');
    Route::delete('/categoria/{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy')->middleware('check.updates:categoria')->middleware('permissao:categoria,false,true');

    Route::get('/produto', [ProdutoController::class, 'index'])->name('produto.index')->middleware('permissao:produto');
    Route::post('/produto', [ProdutoController::class, 'store'])->name('produto.store')->middleware('permissao:produto');
    Route::put('/produto/{produto}', [ProdutoController::class, 'update'])->name('produto.update')->middleware('check.updates:produto')->middleware('permissao:produto,true');
    Route::delete('/produto/{produto}', [ProdutoController::class, 'destroy'])->name('produto.destroy')->middleware('check.updates:produto')->middleware('permissao:produto,false,true');

    Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index')->middleware('permissao:usuario');
    Route::post('/usuario', [UsuarioController::class, 'store'])->name('usuario.store')->middleware('permissao:usuario');
    Route::put('/usuario/{usuario}', [UsuarioController::class, 'update'])->name('usuario.update')->middleware('check.updates:usuario')->middleware('permissao:usuario');
    Route::delete('/usuario/{usuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy')->middleware('check.updates:usuario')->middleware('permissao:usuario');
    Route::patch('/usuario/ativar/{usuario}', [UsuarioController::class, 'ativar'])->middleware('check.updates:usuario')->middleware('permissao:usuario');
});

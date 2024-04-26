<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerCustomValidationsValor();
        $this->registerCustomValidationsUniqueCategoria();
        $this->registerCustomValidationsUniqueProduto();
    }

    public function registerCustomValidationsValor()
    {
        Validator::extend('valor', function ($attribute, $value, $parameters, $validator) {
            $valorString = $value;
            $valorString = str_replace(['R$', ' '], '', $valorString);
            $valorFloat = (float) str_replace(',', '.', $valorString);

            if($valorFloat > 99999999.99) return false;

            return true;
        });
    }  

    public function registerCustomValidationsUniqueCategoria()
    {
        Validator::extend('categoria', function ($attribute, $value, $parameters, $validator) {

            $categorias = DB::select("
                            SELECT c.id, c.nome, c.usuario_id, u.created_by, c.deleted_at 
                            FROM categorias AS c
                            LEFT JOIN users AS u ON u.id = c.usuario_id
                            WHERE (c.usuario_id = ? OR c.usuario_id = ? OR u.created_by = ?) 
                            AND c.deleted_at IS NULL 
                            AND nome = ?
                        ", [Auth::user()->id, Auth::user()->created_by, Auth::user()->id, $value]);
            
        
            if($categorias) return false;
            
            return true;
        });
    }  

    public function registerCustomValidationsUniqueProduto()
    {
        Validator::extend('produto', function ($attribute, $value, $parameters, $validator) {

            $produtos = DB::select("
                            SELECT p.id, p.nome, p.usuario_id, u.created_by, p.deleted_at 
                            FROM produtos AS p
                            LEFT JOIN users AS u ON u.id = p.usuario_id
                            WHERE (p.usuario_id = ? OR p.usuario_id = ? OR u.created_by = ?) 
                            AND p.deleted_at IS NULL 
                            AND nome = ?
                        ", [Auth::user()->id, Auth::user()->created_by, Auth::user()->id, $value]);
            
        
            if($produtos) return false;
            
            return true;
        });
    }  
}

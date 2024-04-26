<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
}

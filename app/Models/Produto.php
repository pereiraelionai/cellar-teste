<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome', 'valor', 'categoria_id'];

    public function categorias() {
        return $this->belongsTo('App\Models\Categoria', 'categoria_id', 'id')->withTrashed();
    }

    public function usuarios() {
        return $this->belongsTo('App\Models\User', 'usuario_id', 'id');
    }
}

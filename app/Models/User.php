<?php

namespace App\Models;

use App\Notifications\VerificarEmailNotification;
use App\Notifications\RedefinirSenhaNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function permissao() {
        return $this->hasOne('App\Models\Permissao', 'usuario_id', 'id');
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new VerificarEmailNotification);
    }

    public  function sendPasswordResetNotification($token)
    {
        $this->notify(new RedefinirSenhaNotification($token, $this->email));
    }
}

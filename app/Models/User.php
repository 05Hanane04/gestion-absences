<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function professeur()
    {
        return $this->hasOne(Professeur::class);
    }

    public function etudiant()
    {
        return $this->hasOne(Etudiant::class);
    }
}
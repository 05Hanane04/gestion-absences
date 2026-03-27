<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    protected $fillable = ['nom', 'code', 'niveau'];

    public function etudiants() { return $this->hasMany(Etudiant::class); }
    public function modules()   { return $this->hasMany(Module::class);   }
}
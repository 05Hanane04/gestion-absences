<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['nom', 'code', 'filiere_id', 'professeur_id', 'volume_horaire'];

    public function filiere()    { return $this->belongsTo(Filiere::class);    }
    public function professeur() { return $this->belongsTo(Professeur::class); }
    public function seances()    { return $this->hasMany(Seance::class);       }
}
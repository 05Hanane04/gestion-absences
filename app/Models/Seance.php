<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    protected $fillable = [
    'module_id', 'date', 'heure_debut', 'heure_fin', 'salle',
    'filiere', 'niveau', 'groupe', 
];

    public function module()   { return $this->belongsTo(Module::class); }
    public function absences() { return $this->hasMany(Absence::class);  }
}
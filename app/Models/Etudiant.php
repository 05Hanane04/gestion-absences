<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $fillable = ['user_id', 'filiere_id', 'cne', 'groupe'];

    public function user()     { return $this->belongsTo(User::class);    }
    public function filiere()  { return $this->belongsTo(Filiere::class); }
    public function absences() { return $this->hasMany(Absence::class);   }
}
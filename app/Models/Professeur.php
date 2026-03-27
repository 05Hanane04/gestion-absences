<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    protected $fillable = ['user_id', 'specialite', 'grade'];

    public function user()    { return $this->belongsTo(User::class); }
    public function modules() { return $this->hasMany(Module::class); }
}
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Filiere;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Module;
use App\Models\Seance;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrateur', 'email' => 'admin@univ.ma',
            'password' => Hash::make('admin123'), 'role' => 'admin',
        ]);
        $iir = Filiere::create(['nom' => 'Ingénierie Informatique et Réseaux', 'code' => '3IIR', 'niveau' => 'Bac+5']);
        $gc  = Filiere::create(['nom' => 'Génie Civil', 'code' => '2GC', 'niveau' => 'Bac+4']);
        $u1 = User::create(['name' => 'Dr. Alami Rachid',  'email' => 'alami@univ.ma',   'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u2 = User::create(['name' => 'Dr. Bennani Souad', 'email' => 'bennani@univ.ma',  'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u3 = User::create(['name' => 'Dr. Fassi Mohamed', 'email' => 'fassi@univ.ma',    'password' => Hash::make('prof123'), 'role' => 'prof']);

        $p1 = Professeur::create(['user_id' => $u1->id, 'specialite' => 'Informatique',   'grade' => 'Professeur']);
        $p2 = Professeur::create(['user_id' => $u2->id, 'specialite' => 'Réseaux',        'grade' => 'Professeur']);
        $p3 = Professeur::create(['user_id' => $u3->id, 'specialite' => 'Mathématiques',  'grade' => 'Professeur']);
        $m1 = Module::create(['nom' => 'Algorithmique', 'code' => 'ALGO', 'filiere_id' => $iir->id, 'professeur_id' => $p1->id, 'volume_horaire' => 40]);
        $m2 = Module::create(['nom' => 'Réseaux',       'code' => 'RES',  'filiere_id' => $iir->id, 'professeur_id' => $p2->id, 'volume_horaire' => 30]);
        $m3 = Module::create(['nom' => 'Maths',         'code' => 'MTH',  'filiere_id' => $iir->id, 'professeur_id' => $p3->id, 'volume_horaire' => 30]);
        $m4 = Module::create(['nom' => 'POO Java',      'code' => 'POO',  'filiere_id' => $iir->id, 'professeur_id' => $p1->id, 'volume_horaire' => 40]);
        $eu1 = User::create(['name' => 'Ali Benali',     'email' => 'etudiant@univ.ma',  'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);
        $eu2 = User::create(['name' => 'Sara Idrissi',   'email' => 'sara@univ.ma',      'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);
        $eu3 = User::create(['name' => 'Youssef Amrani', 'email' => 'youssef@univ.ma',   'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);

        Etudiant::create(['user_id' => $eu1->id, 'filiere_id' => $iir->id, 'cne' => 'R110234567', 'groupe' => 'G1']);
        Etudiant::create(['user_id' => $eu2->id, 'filiere_id' => $iir->id, 'cne' => 'R110234568', 'groupe' => 'G2']);
        Etudiant::create(['user_id' => $eu3->id, 'filiere_id' => $iir->id, 'cne' => 'R110234569', 'groupe' => 'G1']);
        Seance::create(['module_id' => $m1->id, 'date' => '2026-03-24', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'salle' => 'Salle A1']);
        Seance::create(['module_id' => $m2->id, 'date' => '2026-03-24', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'salle' => 'Labo R2']);
        Seance::create(['module_id' => $m3->id, 'date' => '2026-03-25', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'salle' => 'Salle B2']);
        Seance::create(['module_id' => $m4->id, 'date' => '2026-03-26', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'salle' => 'Labo Java']);
    }
}
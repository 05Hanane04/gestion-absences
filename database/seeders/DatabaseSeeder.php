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

        $u1 = User::create(['name' => 'Alami Rachid',   'email' => 'alami@univ.ma',   'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u2 = User::create(['name' => 'Bennani Souad',  'email' => 'bennani@univ.ma',  'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u3 = User::create(['name' => 'Fassi Mohamed',  'email' => 'fassi@univ.ma',    'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u4 = User::create(['name' => 'Tazi Hind',      'email' => 'tazi@univ.ma',     'password' => Hash::make('prof123'), 'role' => 'prof']);
        $u5 = User::create(['name' => 'Cherkaoui Omar', 'email' => 'cherkaoui@univ.ma','password' => Hash::make('prof123'), 'role' => 'prof']);
        $u6 = User::create(['name' => 'Bennani Younes', 'email' => 'younes@univ.ma',   'password' => Hash::make('prof123'), 'role' => 'prof']);

        $p1 = Professeur::create(['user_id' => $u1->id, 'specialite' => 'Informatique',  'grade' => 'Professeur']);
        $p2 = Professeur::create(['user_id' => $u2->id, 'specialite' => 'Réseaux',       'grade' => 'Professeur']);
        $p3 = Professeur::create(['user_id' => $u3->id, 'specialite' => 'Mathématiques', 'grade' => 'Professeur']);
        $p4 = Professeur::create(['user_id' => $u4->id, 'specialite' => 'JavaScript',    'grade' => 'Professeur']);
        $p5 = Professeur::create(['user_id' => $u5->id, 'specialite' => 'Bases de données','grade' => 'Professeur']);
        $p6 = Professeur::create(['user_id' => $u6->id, 'specialite' => 'POO',           'grade' => 'Professeur']);
        $m1 = Module::create(['nom' => 'PHP / Laravel',              'code' => 'PHP',  'filiere_id' => $iir->id, 'professeur_id' => $p1->id, 'volume_horaire' => 40]);
        $m2 = Module::create(['nom' => 'Réseaux Informatiques',      'code' => 'RES',  'filiere_id' => $iir->id, 'professeur_id' => $p2->id, 'volume_horaire' => 30]);
        $m3 = Module::create(['nom' => 'SQL Server',                 'code' => 'SQL',  'filiere_id' => $iir->id, 'professeur_id' => $p3->id, 'volume_horaire' => 30]);
        $m4 = Module::create(['nom' => 'Programmation JavaScript',   'code' => 'JS',   'filiere_id' => $iir->id, 'professeur_id' => $p4->id, 'volume_horaire' => 30]);
        $m5 = Module::create(['nom' => 'Base de Données',            'code' => 'BD',   'filiere_id' => $iir->id, 'professeur_id' => $p5->id, 'volume_horaire' => 30]);
        $m6 = Module::create(['nom' => 'Conception Orientée Objet',  'code' => 'COO',  'filiere_id' => $iir->id, 'professeur_id' => $p6->id, 'volume_horaire' => 30]);

        $eu1 = User::create(['name' => 'Ali Benali',     'email' => 'etudiant@univ.ma', 'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);
        $eu2 = User::create(['name' => 'Sara Idrissi',   'email' => 'sara@univ.ma',     'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);
        $eu3 = User::create(['name' => 'Youssef Amrani', 'email' => 'youssef@univ.ma',  'password' => Hash::make('etudiant123'), 'role' => 'etudiant']);

        $e1 = Etudiant::create(['user_id' => $eu1->id, 'filiere_id' => $iir->id, 'cne' => 'R110234567', 'groupe' => 'G1']);
        $e2 = Etudiant::create(['user_id' => $eu2->id, 'filiere_id' => $iir->id, 'cne' => 'R110234568', 'groupe' => 'G2']);
        $e3 = Etudiant::create(['user_id' => $eu3->id, 'filiere_id' => $iir->id, 'cne' => 'R110234569', 'groupe' => 'G1']);

        $s1 = Seance::create(['module_id' => $m1->id, 'date' => '2026-03-23', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'salle' => 'Salle A1']);

        $s2 = Seance::create(['module_id' => $m3->id, 'date' => '2026-03-24', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'salle' => 'Salle B2']);
        $s3 = Seance::create(['module_id' => $m4->id, 'date' => '2026-03-24', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'salle' => 'Labo Info']);

        $s4 = Seance::create(['module_id' => $m5->id, 'date' => '2026-03-25', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'salle' => 'Labo Java']);

        $s5 = Seance::create(['module_id' => $m6->id, 'date' => '2026-03-26', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'salle' => 'Labo R2']);
        $s6 = Seance::create(['module_id' => $m2->id, 'date' => '2026-03-26', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'salle' => 'Labo R2']);
    }
}
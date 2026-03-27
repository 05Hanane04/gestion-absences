<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@univ.ma'], // condition
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'prof@univ.ma'],
            [
                'name' => 'Prof Alami',
                'password' => Hash::make('prof123'),
                'role' => 'prof',
            ]
        );

        User::updateOrCreate(
            ['email' => 'etudiant@univ.ma'],
            [
                'name' => 'Challal Hanane',
                'password' => Hash::make('etudiant123'),
                'role' => 'etudiant',
            ]
        );
    }
}
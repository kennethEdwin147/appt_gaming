<?php

namespace Database\Seeders;

use App\Models\creator\Creator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur créateur de test pour les connexions
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Creator',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'role' => 'creator',
        ]);

        // Créer le profil créateur
        Creator::create([
            'user_id' => $user->id,
            'bio' => 'Créateur de test pour les développements',
            'platform_name' => 'TestCreator',
            'platform_url' => 'https://example.com',
            'type' => 'content_creator',
            'timezone' => 'Europe/Paris',
        ]);

        $this->command->info('Utilisateur de test créé !');
        $this->command->info('Email: test@test.com');
        $this->command->info('Mot de passe: password');
    }
}

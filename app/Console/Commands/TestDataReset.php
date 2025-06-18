<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TestDataReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:data:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting database for testing...');
        
        // Réinitialiser la base de données
        Artisan::call('migrate:fresh', ['--seed' => true]);
        
        // Créer des utilisateurs de test
        $this->createTestUsers();
        
        $this->info('Database reset successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Créer des utilisateurs de test
     */
    private function createTestUsers()
    {
        $this->info('Creating test users...');
        
        // Créer un utilisateur admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        
        // Créer un utilisateur créateur
        $creator = \App\Models\User::create([
            'name' => 'Creator User',
            'email' => 'creator@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        
        // Créer un profil créateur si le modèle existe
        if (class_exists('\App\Models\Creator')) {
            \App\Models\Creator::create([
                'user_id' => $creator->id,
                'platform_name' => 'Gaming Master',
                'bio' => 'Professional gaming coach with expertise in FPS games',
                'platform_url' => 'https://twitch.tv/gamingmaster',
                'type' => 'Gaming',
                'timezone' => 'America/New_York',
            ]);
        }
        
        // Créer un utilisateur client
        \App\Models\User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        
        $this->info('Test users created successfully!');
    }
}

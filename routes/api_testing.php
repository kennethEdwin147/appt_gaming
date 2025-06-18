<?php

use Illuminate\Support\Facades\Route;

// Ces routes ne sont disponibles qu'en environnement de test ou local
if (app()->environment('local', 'testing')) {
    Route::prefix('testing')->group(function () {
        // Route pour créer un utilisateur de test
        Route::post('/create-user', function () {
            $data = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'sometimes|string|in:admin,creator,customer',
            ]);

            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle si spécifié
            if (isset($data['role'])) {
                // Logique pour assigner le rôle selon votre implémentation
            }

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ]);
        });

        // Route pour nettoyer les données de test
        Route::post('/cleanup', function () {
            // Supprimer les utilisateurs de test créés pendant les tests
            \App\Models\User::where('email', 'like', 'test%@example.com')->delete();

            return response()->json([
                'message' => 'Test data cleaned up successfully',
            ]);
        });
    });
}

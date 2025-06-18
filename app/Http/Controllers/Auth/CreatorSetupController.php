<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\creator\Creator;
use App\Enums\Timezone;
use Illuminate\Support\Str;

class CreatorSetupController extends Controller
{
    public function showTimezoneForm()
    {
        $user = auth()->user();
        $creator = $user->creator;
        
        // If creator record doesn't exist, create it
        if (!$creator) {
            // Générer un pseudo temporaire unique
            $tempPseudo = 'temp_' . Str::random(10);
            
            $creator = Creator::create([
                'user_id' => $user->id,
                'gaming_pseudo' => $tempPseudo, // Valeur temporaire qui sera mise à jour plus tard
                'confirmation_token' => Str::random(60),
            ]);
            
            // Reload the creator
            $creator = $user->fresh()->creator;
        }
        
        // If timezone is already set, redirect to profile setup
        if ($creator->timezone) {
            return redirect()->route('creator.setup.profile');
        }
        
        return view('auth.creator-setup.timezone');
    }
    
    public function saveTimezone(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string',
        ]);
        
        $user = auth()->user();
        $creator = $user->creator;
        
        // If creator record doesn't exist, create it
        if (!$creator) {
            // Générer un pseudo temporaire unique
            $tempPseudo = 'temp_' . Str::random(10);
            
            $creator = Creator::create([
                'user_id' => $user->id,
                'gaming_pseudo' => $tempPseudo, // Valeur temporaire qui sera mise à jour plus tard
                'confirmation_token' => Str::random(60),
            ]);
            
            // Reload the creator
            $creator = $user->fresh()->creator;
        }
        
        $creator->update([
            'timezone' => $request->timezone,
        ]);
        
        return redirect()->route('creator.setup.profile')
            ->with('success', 'Fuseau horaire enregistré avec succès !');
    }
    
    public function showProfileForm()
    {
        $user = auth()->user();
        $creator = $user->creator;
        
        // If creator record doesn't exist, create it and redirect to timezone setup
        if (!$creator) {
            // Générer un pseudo temporaire unique
            $tempPseudo = 'temp_' . Str::random(10);
            
            $creator = Creator::create([
                'user_id' => $user->id,
                'gaming_pseudo' => $tempPseudo, // Valeur temporaire qui sera mise à jour plus tard
                'confirmation_token' => Str::random(60),
            ]);
            
            return redirect()->route('creator.setup.timezone')
                ->with('info', 'Commençons par configurer votre fuseau horaire.');
        }
        
        // If timezone is not set, redirect to timezone setup first
        if (!$creator->timezone) {
            return redirect()->route('creator.setup.timezone')
                ->with('error', 'Veuillez d\'abord configurer votre fuseau horaire.');
        }
        
        return view('auth.creator-setup.profile', compact('creator'));
    }
    
    public function saveProfile(Request $request)
    {
        $user = auth()->user();
        $creator = $user->creator;
        
        // If creator record doesn't exist, redirect to timezone setup
        if (!$creator) {
            return redirect()->route('creator.setup.timezone')
                ->with('error', 'Veuillez d\'abord configurer votre fuseau horaire.');
        }
        
        $request->validate([
            'gaming_pseudo' => 'required|string|max:255|unique:creators,gaming_pseudo,' . $creator->id,
            'bio' => 'required|string|max:1000',
        ]);
        
        $creator->update([
            'gaming_pseudo' => $request->gaming_pseudo,
            'bio' => $request->bio,
            'setup_completed_at' => now(),
        ]);
        
        return redirect()->route('creator.dashboard')
            ->with('success', 'Profil configuré avec succès ! Vous pouvez maintenant commencer à créer des événements.');
    }
}

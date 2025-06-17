<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreatorSetupController extends Controller
{
    public function showTimezone()
    {
        // Si déjà configuré, passer à l'étape suivante
        if (auth()->user()->creator->timezone) {
            return redirect('/creator/setup/profile');
        }
        
        $timezones = $this->getTimezonesByRegion();
        
        return view('authentication.creator-setup.timezone', compact('timezones'));
    }

    public function saveTimezone(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string',
        ]);
        
        auth()->user()->creator->update([
            'timezone' => $request->timezone
        ]);
        
        return redirect('/creator/setup/profile')
            ->with('success', 'Fuseau horaire sauvegardé !');
    }

    public function showProfile()
    {
        $creator = auth()->user()->creator;
        
        // Si pas de timezone, retourner à l'étape 1
        if (!$creator->timezone) {
            return redirect('/creator/setup/timezone');
        }
        
        return view('authentication.creator-setup.profile', compact('creator'));
    }

    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'bio' => 'required|string|max:500',
            'main_game' => 'nullable|string|max:100',
            'rank_info' => 'nullable|string|max:100',
            'default_hourly_rate' => 'required|numeric|min:5|max:500',
        ]);
        
        auth()->user()->creator->update($validated);
        
        return $this->complete();
    }

    public function complete()
    {
        // Marquer le setup comme terminé
        auth()->user()->creator->update([
            'setup_completed_at' => now()
        ]);
        
        return redirect('/creator/dashboard')
            ->with('success', 'Profil créateur configuré avec succès !');
    }

    private function getTimezonesByRegion()
    {
        $timezones = [
            'Europe' => [
                'Europe/Paris' => 'Paris (GMT+1)',
                'Europe/London' => 'Londres (GMT+0)',
                'Europe/Berlin' => 'Berlin (GMT+1)',
                'Europe/Rome' => 'Rome (GMT+1)',
                'Europe/Madrid' => 'Madrid (GMT+1)',
                'Europe/Amsterdam' => 'Amsterdam (GMT+1)',
                'Europe/Brussels' => 'Bruxelles (GMT+1)',
                'Europe/Zurich' => 'Zurich (GMT+1)',
            ],
            'America' => [
                'America/New_York' => 'New York (GMT-5)',
                'America/Chicago' => 'Chicago (GMT-6)',
                'America/Denver' => 'Denver (GMT-7)',
                'America/Los_Angeles' => 'Los Angeles (GMT-8)',
                'America/Toronto' => 'Toronto (GMT-5)',
                'America/Montreal' => 'Montréal (GMT-5)',
                'America/Sao_Paulo' => 'São Paulo (GMT-3)',
            ],
            'Asia' => [
                'Asia/Tokyo' => 'Tokyo (GMT+9)',
                'Asia/Shanghai' => 'Shanghai (GMT+8)',
                'Asia/Singapore' => 'Singapour (GMT+8)',
                'Asia/Dubai' => 'Dubaï (GMT+4)',
                'Asia/Kolkata' => 'Mumbai (GMT+5:30)',
            ],
            'Australia' => [
                'Australia/Sydney' => 'Sydney (GMT+10)',
                'Australia/Melbourne' => 'Melbourne (GMT+10)',
                'Australia/Perth' => 'Perth (GMT+8)',
            ],
        ];

        return $timezones;
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterEmailVerification($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectAfterEmailVerification($request->user());
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterEmailVerification($request->user());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Le lien de vérification a été renvoyé !');
    }

    private function redirectAfterEmailVerification(User $user)
    {
        if ($user->role === 'creator') {
            $creator = $user->creator;
            if (!$creator->timezone || !$creator->bio) {
                return redirect()->route('creator.setup.timezone')
                    ->with('success', 'Email vérifié ! Terminons la configuration.');
            }
            return redirect()->route('creator.dashboard');
        }
        
        return redirect()->route('customer.dashboard')
            ->with('success', 'Email vérifié avec succès !');
    }
}
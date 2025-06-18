<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->redirectAfterEmailVerification(auth()->user());
        }
        
        return view('auth.email.verify-email');
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
            if (!$creator || !$creator->timezone || !$creator->bio) {
                return redirect('/creator/setup/timezone')
                    ->with('success', 'Email vérifié ! Terminons la configuration.');
            }
            return redirect('/creator/dashboard');
        }
        
        return redirect('/customer/dashboard')
            ->with('success', 'Email vérifié avec succès !');
    }
}

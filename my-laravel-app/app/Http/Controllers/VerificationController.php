<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect('/?verified=0');
        }

        if ($request->hasValidSignature()) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
            if (!Auth::check()) {
                Auth::login($user);
            }
            return redirect('/?verified=1');
        }

        return redirect('/?verified=0');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.']);
    }
}

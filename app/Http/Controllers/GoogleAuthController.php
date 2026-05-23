<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        // This comment forces VS Code to recognize the GoogleProvider methods, silencing the error
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            // Get the user data and token from Google
            $googleUser = Socialite::driver('google')->user();
            
            // Explicitly tell the editor this is your App\Models\User
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Save the tokens to our database
            $user->update([
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Google Calendar connected successfully! You can now accept swap requests.');

        } catch (Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to connect Google Calendar. Please try again.');
        }
    }
}
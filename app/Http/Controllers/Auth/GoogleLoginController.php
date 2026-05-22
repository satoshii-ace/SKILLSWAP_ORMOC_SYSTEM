<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleLoginController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Find or create user
            $authUser = $this->findOrCreateUser($user);

            Auth::login($authUser, remember: true);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Failed to authenticate with Google');
        }
    }

    /**
     * Find or create user from Google data
     */
    protected function findOrCreateUser($googleUser)
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Update tokens on subsequent logins
            $user->update([
                'google_access_token' => json_encode(['access_token' => $googleUser->token, 'token_type' => 'Bearer']),
                'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
            ]);
            return $user;
        }

        return User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'google_access_token' => json_encode(['access_token' => $googleUser->token, 'token_type' => 'Bearer']),
            'google_refresh_token' => $googleUser->refreshToken,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(16)), // Generate random password
        ]);
    }
}

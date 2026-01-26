<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = $this->findOrCreateUser($googleUser, 'google');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'Google ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            
            $user = $this->findOrCreateUser($githubUser, 'github');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'GitHub ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'Facebook ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    protected function findOrCreateUser($providerUser, $provider)
    {
        $user = User::where('email', $providerUser->getEmail())->first();

        if ($user) {
            $user->update([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
                'avatar' => $providerUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
                'avatar' => $providerUser->getAvatar(),
                'password' => bcrypt(str_random(16)),
                'role' => 'provider', // Default role
            ]);
        }

        return $user;
    }

    protected function redirectPath()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return '/admin/dashboard';
            case 'statistician':
                return '/statistician/dashboard';
            case 'provider':
                return '/provider/dashboard';
            default:
                return '/home';
        }
    }
}

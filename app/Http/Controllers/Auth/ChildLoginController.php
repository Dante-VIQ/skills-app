<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChildLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.child-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user with child role by username
        $user = User::role('child')
            ->where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'username' => 'The provided credentials do not match our records.',
                ])
                ->withInput($request->only('username'));
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        if (!$user->onboarding_completed) {
            return redirect()->route('child.onboarding');
        }

        return redirect()->route('child.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('child.login');
    }
}
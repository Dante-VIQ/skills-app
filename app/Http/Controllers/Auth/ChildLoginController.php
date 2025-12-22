<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Child;
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

        // Find child by username
        $child = Child::where('username', $request->username)->where('is_active', true)->first();

        // Check if child exists and password is correct
        if (!$child || !Hash::check($request->password, $child->password)) {
            return back()
                ->withErrors([
                    'username' => 'The provided credentials do not match our records.',
                ])
                ->withInput($request->only('username', 'remember'));
        }

        // Manually log in the child
        Auth::guard('child')->login($child, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirect based on onboarding status
        if (!$child->onboarding_completed) {
            return redirect()->route('child.onboarding');
        }

         return redirect()->route('child.home');
    }

    public function logout(Request $request)
    {
        Auth::guard('child')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('child.login');
    }
}

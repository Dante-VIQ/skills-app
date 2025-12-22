<?php
namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $type = 'parent';
    public string $birth_date = '';

public function register()
{
    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'type' => ['required', 'in:parent,child'],
        'birth_date' => ['required_if:type,child', 'nullable', 'date'],
    ]);

    $username = null;
    $birthDate = null;

    if ($validated['type'] === 'child') {
        // Generate username for child
        $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $validated['name']));
        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        
        // Set birth date for child
        $birthDate = $validated['birth_date'];
    }

    // Create user with ALL required fields
    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'], // EMAIL IS REQUIRED FOR ALL USERS
        'password' => Hash::make($validated['password']),
        'type' => $validated['type'],
        'username' => $username,
        'birth_date' => $birthDate,
        'onboarding_completed' => $validated['type'] === 'parent',
    ];

    $user = User::create($userData);

    event(new Registered($user));
    Auth::login($user);

    return $this->redirect(
        $user->isParent()
            ? route('parent.dashboard')
            : ($user->onboarding_completed
                ? route('child.dashboard')
                : route('child.onboarding')),
        navigate: true
    );
}

public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}

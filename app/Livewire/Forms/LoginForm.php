<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Services\Discovery\AutomatedContentDiscovery;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $login = ''; // Changed from 'email' to 'login' to accept email or username

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Determine if login is email or username
        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find the user first to check if they exist and are active
        $user = User::where($field, $this->login)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'form.login' => trans('auth.failed'),
            ]);
        }

        // Attempt authentication with the correct field
        $credentials = [
            $field => $this->login,
            'password' => $this->password
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'form.login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }

    protected function authenticated(Request $request, $user)
{
    // Trigger content discovery on login
    if ($user->interests()->exists()) {
        dispatch(function () use ($user) {
            app(AutomatedContentDiscovery::class)->discoverForUser($user, 20);
        })->afterResponse();
    }
    
    return redirect()->intended($this->redirectPath());
}
}

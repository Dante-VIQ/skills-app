<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        try {
            $this->form->authenticate();

            Session::regenerate();

            $user = Auth::user();
            $role = $user->getRoleNames()->first();

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Redirect based on role
            if ($user->hasRole('child') && !$user->onboarding_completed) {
                $this->redirectRoute('child.onboarding', navigate: true);
                return;
            }

            match($role) {
                'parent' => $this->redirectRoute('parent.dashboard', navigate: true),
                'child' => $this->redirectRoute('child.dashboard', navigate: true),
                'tutor' => $this->redirectRoute('tutor.dashboard', navigate: true),
                'master' => $this->redirectRoute('master.dashboard', navigate: true),
                default => $this->redirectRoute('home', navigate: true),
            };

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw to let Livewire handle the validation errors
            throw $e;
        } catch (\Exception $e) {
            $this->addError('form.login', 'An error occurred during login. Please try again.');
        }
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Role Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex space-x-4">
            <a href="{{ route('login') }}" 
               class="pb-2 px-1 text-sm font-medium {{ request()->routeIs('login') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-users mr-1"></i> All Users
            </a>
            <a href="{{ route('child.login') }}" 
               class="pb-2 px-1 text-sm font-medium {{ request()->routeIs('child.login') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-gray-700' }}">
                <i class="fas fa-child mr-1"></i> Kids' Login
            </a>
        </div>
    </div>

    <!-- Role Info Cards -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
            <div class="flex items-center">
                <i class="fas fa-user text-blue-600 text-xl mr-3"></i>
                <div>
                    <p class="text-xs font-medium text-blue-800">Parent</p>
                    <p class="text-xs text-blue-600">Use email</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 p-3 rounded-lg border border-green-200">
            <div class="flex items-center">
                <i class="fas fa-child text-green-600 text-xl mr-3"></i>
                <div>
                    <p class="text-xs font-medium text-green-800">Student</p>
                    <p class="text-xs text-green-600">Use username</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
            <div class="flex items-center">
                <i class="fas fa-chalkboard-teacher text-purple-600 text-xl mr-3"></i>
                <div>
                    <p class="text-xs font-medium text-purple-800">Tutor</p>
                    <p class="text-xs text-purple-600">Use email</p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="login">
        <!-- Login Field (Email or Username) -->
        <div>
            <x-input-label for="login" :value="__('Email or Username')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <x-text-input wire:model="form.login"
                             id="login"
                             class="block mt-1 w-full pl-10"
                             type="text"
                             name="login"
                             placeholder="Enter your email or username"
                             required
                             autofocus />
            </div>
            @if(strlen($form->login) > 0)
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Logging in as:
                    <span class="font-medium">
                        {{ filter_var($form->login, FILTER_VALIDATE_EMAIL) ? 'Email (Parent/Tutor)' : 'Username (Student)' }}
                    </span>
                </p>
            @endif
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input wire:model="form.password"
                             id="password"
                             class="block mt-1 w-full pl-10"
                             type="password"
                             name="password"
                             required />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember"
                       id="remember"
                       type="checkbox"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                       name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                   href="{{ route('password.request') }}"
                   wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                <span wire:loading.remove wire:target="login">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ __('Log in') }}
                </span>
                <span wire:loading wire:target="login">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Logging in...
                </span>
            </x-primary-button>
        </div>

        <!-- Registration Link -->
        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                New to SkillDNA?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium" wire:navigate>
                    Create an account
                </a>
            </p>

            <!-- Role-specific registration info -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                <div class="p-2 bg-blue-50 rounded">
                    <span class="font-medium text-blue-700">Parents:</span> Register free
                </div>
                <div class="p-2 bg-green-50 rounded">
                    <span class="font-medium text-green-700">Students:</span> Need parent code
                </div>
                <div class="p-2 bg-purple-50 rounded">
                    <span class="font-medium text-purple-700">Tutors:</span> Invitation only
                </div>
            </div>
        </div>
    </form>
</div>
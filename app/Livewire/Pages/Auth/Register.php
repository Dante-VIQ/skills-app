<?php

namespace App\Livewire\Pages\Auth;

use App\Models\TutorInvitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    // Basic info
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $username = '';
    public $password = '';
    public $password_confirmation = '';
    public $user_role = 'parent';
    
    // Child fields
    public $parent_invite_code = '';
    public $parent_name = null;
    public $show_child_fields = false;
    
    // Tutor fields
    public $tutor_invite_code = '';
    public $show_tutor_fields = false;

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_role' => 'required|in:parent,child,tutor',
        ];

        if ($this->user_role === 'child') {
            $rules['parent_invite_code'] = 'required|string|size:8|exists:users,parent_code';
        }

        if ($this->user_role === 'tutor') {
            $rules['tutor_invite_code'] = 'required|string|size:12';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'parent_invite_code.required' => 'Please enter your parent\'s invitation code.',
            'parent_invite_code.size' => 'Parent code must be exactly 8 characters.',
            'parent_invite_code.exists' => 'This parent code is invalid.',
            'tutor_invite_code.required' => 'Please enter your tutor invitation code.',
            'tutor_invite_code.size' => 'Tutor code must be exactly 12 characters.',
            'email.unique' => 'This email is already registered.',
            'username.unique' => 'This username is already taken.',
        ];
    }

    public function mount()
    {
        // Check for invite code in URL (parent sharing link)
        if (request()->has('invite')) {
            $this->user_role = 'child';
            $this->parent_invite_code = request()->get('invite');
            $this->show_child_fields = true;
            $this->validateParentCode();
        }
    }

    public function updatedUserRole($value)
    {
        $this->resetErrorBag();
        $this->parent_invite_code = '';
        $this->tutor_invite_code = '';
        $this->parent_name = null;
        
        $this->show_child_fields = ($value === 'child');
        $this->show_tutor_fields = ($value === 'tutor');
    }

    public function updatedParentInviteCode($value)
    {
        if (strlen($value) === 8) {
            $this->validateParentCode();
        } else {
            $this->parent_name = null;
        }
    }

    public function validateParentCode()
    {
        $parent = User::role('parent')
            ->where('parent_code', $this->parent_invite_code)
            ->first();

        if ($parent) {
            $this->parent_name = $parent->full_name;
            return true;
        } else {
            $this->parent_name = null;
            $this->addError('parent_invite_code', 'Invalid parent invitation code.');
            return false;
        }
    }

    public function register()
    {
        $this->validate();

        try {
            // Additional validation for child
            $parent = null;
            if ($this->user_role === 'child') {
                $parent = User::role('parent')
                    ->where('parent_code', $this->parent_invite_code)
                    ->first();
                
                if (!$parent) {
                    $this->addError('parent_invite_code', 'Parent not found.');
                    return;
                }
            }

            // Additional validation for tutor
            $invitation = null;
            if ($this->user_role === 'tutor') {
                $invitation = TutorInvitation::where('code', $this->tutor_invite_code)
                    ->where('used', false)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->first();

                if (!$invitation) {
                    $this->addError('tutor_invite_code', 'Invalid or expired tutor invitation.');
                    return;
                }
            }

            // Prepare user data
            $userData = [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'username' => $this->username,
                'password' => Hash::make($this->password),
                'is_active' => true,
            ];

            // Role-specific data
            if ($this->user_role === 'parent') {
                $userData['parent_code'] = $this->generateParentCode();
            }

            if ($this->user_role === 'child' && $parent) {
                $userData['parent_id'] = $parent->id;
                $userData['email_verified_at'] = now(); // Auto-verify children
            }

            // Create user
            $user = User::create($userData);

            // Assign role
            $user->assignRole($this->user_role);

            // Mark tutor invitation as used
            if ($this->user_role === 'tutor' && $invitation) {
                $invitation->update([
                    'used' => true,
                    'used_by' => $user->id,
                    'used_at' => now(),
                ]);
            }

            // Log registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'role' => $this->user_role,
                'email' => $user->email
            ]);

            // Fire registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            // Redirect based on role
            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            $this->addError('form', 'Registration failed. Please try again.');
        }
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->isChild() && !$user->onboarding_completed) {
            return redirect()->route('child.onboarding');
        }

        return match($user->getRoleNames()->first()) {
            'parent' => redirect()->route('parent.dashboard'),
            'child' => redirect()->route('child.dashboard'),
            'tutor' => redirect()->route('tutor.dashboard'),
            'master' => redirect()->route('master.dashboard'),
            default => redirect()->route('home'),
        };
    }

    protected function generateParentCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('parent_code', $code)->exists());

        return $code;
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
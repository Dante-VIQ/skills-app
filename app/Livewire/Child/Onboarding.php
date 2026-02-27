<?php

namespace App\Livewire\Child;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Onboarding extends Component
{
    public $step = 1;
    public $selectedInterests = [];
    public $answers = [];

    public function mount()
    {
        // Redirect if already onboarded
        $user = Auth::user();
        if ($user && $user->onboarding_completed) {
            return $this->redirect(route('child.home'), navigate: true);
        }
    }

    public function complete()
    {
        $user = Auth::user();

        if (!$user || !$user->isChild()) {
            return;
        }

        // Save onboarding data
        $onboardingData = [
            'interests' => $this->selectedInterests,
            'answers' => $this->answers,
            'completed_at' => now()->toDateTimeString(),
        ];

        $user->update([
            'onboarding_completed' => true,
            'onboarding_data' => $onboardingData,
        ]);

        // Redirect to dashboard
        return $this->redirect(route('child.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.child.onboarding');
    }
}

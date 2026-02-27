<?php

namespace App\Http\Controllers\Child;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:child']);
    }

    /**
     * Show onboarding welcome page
     */
    public function index()
    {
        $child = auth()->user();

        // If already completed onboarding, redirect to dashboard
        if ($child->onboarding_completed) {
            return redirect()->route('child.dashboard');
        }

        return view('child.onboarding.index');
    }

    /**
     * Step 1: Choose Avatar
     */
    public function step1()
    {
        $child = auth()->user();

        if ($child->onboarding_completed) {
            return redirect()->route('child.dashboard');
        }

        return view('child.onboarding.step1');
    }

    /**
     * Save Avatar
     */
    public function saveAvatar(Request $request)
    {
        $child = auth()->user();

        $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'avatar_type' => 'required|in:upload,emoji,default',
            'avatar_emoji' => 'required_if:avatar_type,emoji|string',
        ]);

        try {
            if ($request->avatar_type === 'upload' && $request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($child->avatar) {
                    Storage::disk('public')->delete($child->avatar);
                }
                
                $path = $request->file('avatar')->store('avatars/children', 'public');
                $child->avatar = $path;
                
            } elseif ($request->avatar_type === 'emoji') {
                // Store emoji as avatar (you can store emoji code or use avatars library)
                $child->avatar = 'emoji:' . $request->avatar_emoji;
                
            } else {
                // Default avatar - generate based on initials
                $child->avatar = null; // Will use initials in view
            }

            $child->save();

            // Store progress in session
            session(['onboarding_step' => 2]);

            return response()->json([
                'success' => true,
                'next_url' => route('child.onboarding.step2')
            ]);

        } catch (\Exception $e) {
            Log::error('Avatar save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save avatar. Please try again.'
            ], 500);
        }
    }

    /**
     * Step 2: Choose Interests
     */
    public function step2()
    {
        $child = auth()->user();

        if ($child->onboarding_completed) {
            return redirect()->route('child.dashboard');
        }

        // Predefined interests for children
        $interests = [
            ['id' => 'science', 'name' => 'Science', 'icon' => '🔬', 'color' => 'blue'],
            ['id' => 'math', 'name' => 'Math', 'icon' => '🧮', 'color' => 'green'],
            ['id' => 'reading', 'name' => 'Reading', 'icon' => '📚', 'color' => 'purple'],
            ['id' => 'art', 'name' => 'Art', 'icon' => '🎨', 'color' => 'pink'],
            ['id' => 'music', 'name' => 'Music', 'icon' => '🎵', 'color' => 'yellow'],
            ['id' => 'coding', 'name' => 'Coding', 'icon' => '💻', 'color' => 'indigo'],
            ['id' => 'animals', 'name' => 'Animals', 'icon' => '🦁', 'color' => 'orange'],
            ['id' => 'space', 'name' => 'Space', 'icon' => '🚀', 'color' => 'gray'],
            ['id' => 'sports', 'name' => 'Sports', 'icon' => '⚽', 'color' => 'red'],
            ['id' => 'history', 'name' => 'History', 'icon' => '🏛️', 'color' => 'amber'],
            ['id' => 'nature', 'name' => 'Nature', 'icon' => '🌿', 'color' => 'emerald'],
            ['id' => 'cooking', 'name' => 'Cooking', 'icon' => '🍳', 'color' => 'rose'],
        ];

        return view('child.onboarding.step2', compact('interests'));
    }

    /**
     * Save Interests
     */
    public function saveInterests(Request $request)
    {
        $child = auth()->user();

        $request->validate([
            'interests' => 'required|array|min:3|max:6',
            'interests.*' => 'string|in:science,math,reading,art,music,coding,animals,space,sports,history,nature,cooking',
        ]);

        try {
            $child->interests = $request->interests;
            $child->save();

            // Store progress in session
            session(['onboarding_step' => 3]);

            return response()->json([
                'success' => true,
                'next_url' => route('child.onboarding.step3')
            ]);

        } catch (\Exception $e) {
            Log::error('Interests save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save interests. Please try again.'
            ], 500);
        }
    }

    /**
     * Step 3: Learning Goals
     */
    public function step3()
    {
        $child = auth()->user();

        if ($child->onboarding_completed) {
            return redirect()->route('child.dashboard');
        }

        $goals = [
            ['id' => 'improve_grades', 'name' => 'Improve my grades', 'icon' => '📊'],
            ['id' => 'learn_new_skill', 'name' => 'Learn a new skill', 'icon' => '🎯'],
            ['id' => 'have_fun', 'name' => 'Have fun learning', 'icon' => '😊'],
            ['id' => 'help_others', 'name' => 'Help others', 'icon' => '🤝'],
            ['id' => 'earn_rewards', 'name' => 'Earn rewards', 'icon' => '🏆'],
            ['id' => 'make_friends', 'name' => 'Make friends', 'icon' => '👥'],
        ];

        return view('child.onboarding.step3', compact('goals'));
    }

    /**
     * Save Learning Goals
     */
    public function saveGoals(Request $request)
    {
        $child = auth()->user();

        $request->validate([
            'goals' => 'required|array|min:1',
            'goals.*' => 'string',
        ]);

        try {
            // Store goals in a separate table or JSON field
            $child->learning_goals = $request->goals;
            $child->save();

            // Store progress in session
            session(['onboarding_step' => 4]);

            return response()->json([
                'success' => true,
                'next_url' => route('child.onboarding.step4')
            ]);

        } catch (\Exception $e) {
            Log::error('Goals save failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save goals. Please try again.'
            ], 500);
        }
    }

    /**
     * Step 4: Welcome Video / Tour
     */
    public function step4()
    {
        $child = auth()->user();

        if ($child->onboarding_completed) {
            return redirect()->route('child.dashboard');
        }

        return view('child.onboarding.step4');
    }

    /**
     * Complete Onboarding
     */
    public function complete(Request $request)
    {
        $child = auth()->user();

        try {
            // Mark onboarding as completed
            $child->onboarding_completed = true;
            $child->save();

            // Clear onboarding session
            session()->forget('onboarding_step');

            // Log onboarding completion
            Log::info('Child completed onboarding', [
                'child_id' => $child->id,
                'interests' => $child->interests
            ]);

            // Record activity
            $child->activities()->create([
                'type' => 'onboarding_completed',
                'title' => 'Completed Onboarding',
                'description' => 'Set up profile with ' . count($child->interests ?? []) . ' interests',
                'status' => 'completed',
            ]);

            return redirect()->route('child.dashboard')
                ->with('success', '🎉 Welcome to SkillDNA! Your learning adventure begins now!');

        } catch (\Exception $e) {
            Log::error('Onboarding completion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Skip onboarding (optional)
     */
    public function skip()
    {
        $child = auth()->user();

        try {
            $child->update([
                'onboarding_completed' => true,
                'interests' => ['general'], // Default interest
            ]);

            session()->forget('onboarding_step');

            return redirect()->route('child.dashboard')
                ->with('info', 'You can complete your profile anytime from settings.');

        } catch (\Exception $e) {
            Log::error('Onboarding skip failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Get current step
     */
    public function getCurrentStep()
    {
        $step = session('onboarding_step', 1);
        return response()->json(['step' => $step]);
    }
}
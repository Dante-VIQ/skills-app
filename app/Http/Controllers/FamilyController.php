<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FamilyController extends Controller
{
    public function create()
    {
        return view('family.add-child');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Generate unique username
        $baseUsername = Str::slug($request->first_name);
        $username = $baseUsername;
        $counter = 1;

        while (Child::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        // Generate initial password
        // $password = Str::random(8); // Or use: $username . '123'
        $birthYear = date('Y', strtotime($request->birth_date));
        $password = strtolower($request->first_name) . $birthYear; // Example: daniel2019
        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/children', 'public');
        }

        // Create child record
        $child = Child::create([
            'parent_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'email' => $request->email, // Optional, not unique
            'birth_date' => $request->birth_date,
            'password' => Hash::make($password),
            'onboarding_completed' => false,
            'avatar' => $avatarPath,
            'settings' => json_encode([
                'theme' => 'light',
                'language' => 'en',
                'notifications' => true,
                'daily_limit' => 60, // minutes
            ]),
        ]);

        // You can send login details to parent via email
        // Mail::to(Auth::user()->email)->send(new ChildAccountCreated($child, $password));

        return redirect()
            ->route('dashboard')
            ->with('success', "{$child->first_name} has been added to your family!")
            ->with('child_details', [
                'username' => $child->username,
                'password' => $password, // Show only once
            ]);
    }

    public function edit(Child $child)
    {
        // Check if child belongs to authenticated parent
        if ($child->parent_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('parent.edit-child', compact('child'));
    }

    public function update(Request $request, Child $child)
    {
        // Check if child belongs to authenticated parent
        if ($child->parent_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($child->avatar) {
                Storage::disk('public')->delete($child->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars/children', 'public');
        } else {
            $avatarPath = $child->avatar;
        }

        $child->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
            'avatar' => $avatarPath,
            'is_active' => $request->boolean('is_active', $child->is_active),
        ]);

        return redirect()
            ->route('parent.dashboard')
            ->with('success', "{$child->first_name}'s profile has been updated!");
    }

    public function destroy(Child $child)
    {
        // Check if child belongs to authenticated parent
        if ($child->parent_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete or deactivate instead of permanent delete
        $child->update(['is_active' => false]);
        // OR: $child->delete(); if using soft deletes

        return redirect()
            ->route('parent.dashboard')
            ->with('success', "{$child->first_name} has been removed from your family.");
    }
}

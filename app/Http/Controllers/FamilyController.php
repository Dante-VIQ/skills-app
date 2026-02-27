<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FamilyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:parent']);
    }
    
    public function create()
    {
        $this->authorize('manage children');
        
        return view('family.add-child');
    }
    
    public function store(Request $request)
    {
        $this->authorize('manage children');
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'date_of_birth' => 'required|date|before:today',
        ]);
        
        // Create child user
        $child = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->username . '@skilldna.child', // Temporary email
            'password' => bcrypt(Str::random(16)), // Temporary password
            'date_of_birth' => $request->date_of_birth,
            'parent_id' => auth()->id(),
            'onboarding_completed' => false,
            'is_active' => true,
        ]);
        
        // Assign child role
        $child->assignRole('child');
        
        // Generate child's login token/password
        $loginCode = Str::random(8);
        $child->update(['initial_password' => $loginCode]);
        
        return redirect()->route('parent.dashboard')
            ->with('success', 'Child account created! Login code: ' . $loginCode);
    }
}
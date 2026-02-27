<?php

namespace App\Http\Controllers\Master;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:master']);
    }
    
    public function index()
    {
        $this->authorize('manage all users');
        
        $users = User::with('roles')
            ->withCount(['activities', 'receivedAssignments', 'assignedAssignments'])
            ->latest()
            ->paginate(25);
            
        $roles = Role::all();
        
        return view('master.users.index', compact('users', 'roles'));
    }
    
    public function create()
    {
        $this->authorize('manage all users');
        
        $roles = Role::all();
        
        return view('master.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $this->authorize('manage all users');
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
            'parent_id' => 'nullable|exists:users,id',
            'date_of_birth' => 'nullable|date',
        ]);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'parent_id' => $request->parent_id,
            'date_of_birth' => $request->date_of_birth,
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole($request->role);
        
        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Created new user with role: ' . $request->role);
        
        return redirect()->route('master.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function edit(User $user)
    {
        $this->authorize('manage all users');
        
        $roles = Role::all();
        $parents = User::role('parent')->get();
        
        return view('master.users.edit', compact('user', 'roles', 'parents'));
    }
    
    public function update(Request $request, User $user)
    {
        $this->authorize('manage all users');
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role' => 'required|exists:roles,name',
            'parent_id' => 'nullable|exists:users,id',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean',
        ]);
        
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'parent_id' => $request->parent_id,
            'date_of_birth' => $request->date_of_birth,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Update role if changed
        if (!$user->hasRole($request->role)) {
            $user->syncRoles([$request->role]);
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('Changed user role to: ' . $request->role);
        }
        
        return redirect()->route('master.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function destroy(User $user)
    {
        $this->authorize('manage all users');
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Deleted user');
        
        return redirect()->route('master.users.index')
            ->with('success', 'User deleted successfully.');
    }
    
    public function toggleStatus(User $user)
    {
        $this->authorize('manage all users');
        
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log("User {$status}");
        
        return back()->with('success', "User {$status} successfully.");
    }
    
    public function impersonate(User $user)
    {
        $this->authorize('manage all users');
        
        // Store original user ID
        session(['original_user_id' => auth()->id()]);
        
        // Log in as the target user
        auth()->login($user);
        
        // Log the impersonation
        activity()
            ->causedBy(User::find(session('original_user_id')))
            ->performedOn($user)
            ->log('Started impersonating user');
        
        // Redirect to appropriate dashboard
        return match($user->getRoleNames()->first()) {
            'parent' => redirect()->route('parent.dashboard'),
            'child' => redirect()->route('child.dashboard'),
            'tutor' => redirect()->route('tutor.dashboard'),
            'master' => redirect()->route('master.dashboard'),
            default => redirect()->route('home'),
        };
    }
    
    public function stopImpersonating()
    {
        if (!session('original_user_id')) {
            return redirect()->route('master.dashboard');
        }
        
        $originalUser = User::find(session('original_user_id'));
        $currentUser = auth()->user();
        
        auth()->login($originalUser);
        session()->forget('original_user_id');
        
        // Log the end of impersonation
        activity()
            ->causedBy($originalUser)
            ->performedOn($currentUser)
            ->log('Stopped impersonating user');
        
        return redirect()->route('master.users.index')
            ->with('info', 'Returned to admin account.');
    }
}
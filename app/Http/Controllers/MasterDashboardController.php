<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ContentModule;
use Illuminate\Routing\Controller;
use App\Models\SocialImpactProject;

class MasterDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:master']);
    }
    
    public function index()
    {
        // Platform statistics
        $stats = [
            'total_users' => User::count(),
            'active_children' => User::role('child')->where('is_active', true)->count(),
            'active_parents' => User::role('parent')->where('is_active', true)->count(),
            'total_content' => ContentModule::where('is_active', true)->count(),
            'active_projects' => SocialImpactProject::where('status', 'active')->count(),
            'total_impact' => \App\Models\ImpactContribution::sum('contribution_value'),   // ✅ change this
            'today_activities' => \App\Models\ChildActivity::whereDate('created_at', today())->count(),
        ];
        
        // Recent registrations
        $recentRegistrations = User::with('roles')
            ->latest()
            ->take(10)
            ->get();
        
        // Platform growth (last 30 days)
        $growthData = $this->getGrowthData();
        
        // Recent activities
        $recentActivities = \App\Models\ChildActivity::with(['child', 'contentModule'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('master.dashboard', compact(
            'stats',
            'recentRegistrations',
            'growthData',
            'recentActivities'
        ));
    }
    
    public function users()
    {
        $this->authorize('manage all users');
        
        $users = User::with('roles')
            ->withCount(['activities', 'assignments'])
            ->paginate(25);
        
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('master.users.index', compact('users', 'roles'));
    }
    
    public function updateUserRole(Request $request, User $user)
    {
        $this->authorize('manage all users');
        
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);
        
        $user->syncRoles([$request->role]);
        
        // Log the role change
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Changed user role to ' . $request->role);
        
        return back()->with('success', 'User role updated successfully.');
    }
    
    private function getGrowthData()
    {
        $data = [];
        
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $data[] = [
                'date' => $date->format('M d'),
                'registrations' => User::whereDate('created_at', $date)->count(),
                'activities' => \App\Models\ChildActivity::whereDate('created_at', $date)->count(),
            ];
        }
        
        return $data;
    }
}
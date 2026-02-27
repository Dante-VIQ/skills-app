<?php

namespace App\Http\Controllers;

use App\Mail\ChildCredentialsMail;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ParentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:parent']);
    }

    public function index()
    {
        $parent = auth()->user();

        // Get children with counts
        $children = $parent->children()
            ->withCount([
                'receivedAssignments as pending_assignments_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'receivedAssignments as completed_assignments_count' => function ($query) {
                    $query->where('status', 'completed');
                },
                'activities as total_activities_count',
                'activities as recent_activities_count' => function ($query) {
                    $query->where('created_at', '>=', now()->subDays(7));
                }
            ])
            ->get();

        // Get recent activities across all children
        $recentActivities = ChildActivity::whereIn('child_id', $children->pluck('id'))
            ->with('child')
            ->latest()
            ->take(10)
            ->get();

        // Get upcoming assignments
        $upcomingAssignments = ParentAssignment::whereIn('child_id', $children->pluck('id'))
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('child')
            ->orderBy('due_date')
            ->get();

        return view('parent.dashboard', compact(
            'children',
            'recentActivities',
            'upcomingAssignments'
        ));
    }

    /**
     * Show the form to add a new child
     */
    public function addChildForm()
    {
        return view('parent.add-child');
    }

    public function children()
    {
        $parent = auth()->user();
        $children = $parent->children()->withCount('receivedAssignments')->get();

        return view('parent.dashboard', compact('children'));
    }

    public function viewChild(User $child)
    {
        $parent = auth()->user();

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized access to child data.');
        }

        $activities = $child->activities()->with('contentModule')->latest()->paginate(20);
        $assignments = $child->receivedAssignments()->latest()->get();

        return view('parent.children.view', compact('child', 'activities', 'assignments'));
    }
/**
     * Add a new child and send credentials via email
     */
    public function addChild(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'date_of_birth' => 'required|date|before:today',
        ]);
        
        $parent = auth()->user();
        
        // Generate a secure temporary password
        $temporaryPassword = $this->generateSecurePassword();
        
        try {
            // Create child user
            $child = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->username . '@skilldna.child', // Generate a placeholder email
                'password' => Hash::make($temporaryPassword),
                'date_of_birth' => $request->date_of_birth,
                'parent_id' => $parent->id,
                'onboarding_completed' => false,
                'is_active' => true,
            ]);
            
            // Assign child role
            $child->assignRole('child');
            
            // Log child creation
            Log::info('Child account created', [
                'parent_id' => $parent->id,
                'child_id' => $child->id,
                'child_username' => $child->username
            ]);
            
            // Send email with credentials
            $this->sendCredentialsEmail($parent, $child, $temporaryPassword);
            
            return redirect()->route('parent.children')
                ->with('success', 'Child account created successfully! Login credentials have been sent to your email.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create child account', [
                'error' => $e->getMessage(),
                'parent_id' => $parent->id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create child account. Please try again.');
        }
    }
    
    /**
     * Send credentials email with retry logic
     */
    private function sendCredentialsEmail($parent, $child, $temporaryPassword, $retryCount = 0)
    {
        try {
            Mail::to($parent->email)
                ->send(new ChildCredentialsMail($parent, $child, $temporaryPassword));
            
            Log::info('Child credentials email sent successfully', [
                'parent_email' => $parent->email,
                'child_id' => $child->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send child credentials email', [
                'error' => $e->getMessage(),
                'parent_email' => $parent->email,
                'child_id' => $child->id,
                'retry_count' => $retryCount
            ]);
            
            // Retry once after a delay
            if ($retryCount < 1) {
                sleep(2); // Wait 2 seconds
                return $this->sendCredentialsEmail($parent, $child, $temporaryPassword, $retryCount + 1);
            }
            
            // If still failing, we need to show the password to the parent
            session()->flash('warning', "Child added but email couldn't be sent. Temporary password: {$temporaryPassword}");
            return false;
        }
    }
    
    /**
     * Generate a secure password
     */
    private function generateSecurePassword(): string
    {
        $length = 12;
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%';
        
        $all = $uppercase . $lowercase . $numbers . $symbols;
        
        // Ensure at least one of each type
        $password = 
            $uppercase[random_int(0, strlen($uppercase) - 1)] .
            $lowercase[random_int(0, strlen($lowercase) - 1)] .
            $numbers[random_int(0, strlen($numbers) - 1)] .
            $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill the rest randomly
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Shuffle to avoid predictable pattern
        return str_shuffle($password);
    }
    
    /**
     * Resend credentials to parent
     */
    public function resendCredentials(User $child)
    {
        $parent = auth()->user();
        
        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized access.');
        }
        
        try {
            // Generate a new temporary password
            $temporaryPassword = $this->generateSecurePassword();
            
            // Update child's password
            $child->password = Hash::make($temporaryPassword);
            $child->save();
            
            // Send email
            Mail::to($parent->email)->send(new ChildCredentialsMail($parent, $child, $temporaryPassword));
            
            return back()->with('success', 'New login credentials have been sent to your email.');
            
        } catch (\Exception $e) {
            Log::error('Failed to resend credentials: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please try again.');
        }
    }
}

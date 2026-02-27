<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TutorInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TutorInvitationEmail;

class InvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:master']);
    }
    
    public function index()
    {
        $invitations = TutorInvitation::with(['creator', 'user'])
            ->latest()
            ->paginate(20);
            
        return view('master.invitations.index', compact('invitations'));
    }
    
    public function create()
    {
        return view('master.invitations.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);
        
        $invitation = TutorInvitation::create([
            'code' => TutorInvitation::generateCode(),
            'email' => $request->email,
            'name' => $request->name,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'expires_at' => $request->expires_in_days 
                ? now()->addDays($request->expires_in_days)
                : now()->addMonth(),
        ]);
        
        // Send email if provided
        if ($request->email) {
            Mail::to($request->email)->send(new TutorInvitationEmail($invitation));
        }
        
        return redirect()->route('master.invitations.index')
            ->with('success', 'Invitation created successfully.')
            ->with('invitation_code', $invitation->code); // Show code for manual sharing
    }
    
    public function resend(TutorInvitation $invitation)
    {
        if (!$invitation->email) {
            return back()->with('error', 'No email address associated with this invitation.');
        }
        
        Mail::to($invitation->email)->send(new TutorInvitationEmail($invitation));
        
        return back()->with('success', 'Invitation resent successfully.');
    }
    
    public function destroy(TutorInvitation $invitation)
    {
        $invitation->delete();
        
        return back()->with('success', 'Invitation deleted successfully.');
    }
}
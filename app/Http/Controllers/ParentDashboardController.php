<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function index()
    {
        // Get all children for the logged-in parent
        $children = Child::where('parent_id', Auth::id())->active()->get();
        
        // You can also add other data you need
        $stats = [
            'total_children' => $children->count(),
            'active_children' => $children->where('onboarding_completed', true)->count(),
            'pending_onboarding' => $children->where('onboarding_completed', false)->count(),
        ];
        
        return view('parent.dashboard', compact('children', 'stats'));
    }

}
<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ContentCategory;
use App\Models\ContentModule;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:master']);
    }
    
    public function index()
    {
        $this->authorize('manage all content');
        
        $categories = ContentCategory::withCount('contentModules')->get();
        $modules = ContentModule::with('category')
            ->latest()
            ->paginate(20);
            
        return view('master.content.index', compact('categories', 'modules'));
    }
    
    public function categories()
    {
        $this->authorize('manage all content');
        
        $categories = ContentCategory::withCount('contentModules')->get();
        
        return view('master.content.categories', compact('categories'));
    }
    
    public function storeCategory(Request $request)
    {
        $this->authorize('manage all content');
        
        $request->validate([
            'name' => 'required|string|max:255|unique:content_categories',
            'description' => 'nullable|string',
            'color' => 'required|string',
            'icon' => 'nullable|string',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
        ]);
        
        ContentCategory::create($request->all());
        
        return back()->with('success', 'Category created successfully.');
    }
    
    public function createModule()
    {
        $this->authorize('manage all content');
        
        $categories = ContentCategory::all();
        
        return view('master.content.modules.create', compact('categories'));
    }
    
    public function storeModule(Request $request)
    {
        $this->authorize('manage all content');
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content_category_id' => 'required|exists:content_categories,id',
            'content_type' => 'required|in:video,article,quiz,interactive',
            'duration' => 'nullable|integer|min:1',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0|gte:min_age',
            'is_active' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048',
            'content_url' => 'nullable|url',
            'embedded_content' => 'nullable|string',
        ]);
        
        $moduleData = $request->except('thumbnail');
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('content/thumbnails', 'public');
            $moduleData['thumbnail'] = $path;
        }
        
        $module = ContentModule::create($moduleData);
        
        // Associate interests and skills if provided
        if ($request->has('interests')) {
            $module->interests()->sync($request->interests);
        }
        
        if ($request->has('skills')) {
            $module->skills()->sync($request->skills);
        }
        
        return redirect()->route('master.content.index')
            ->with('success', 'Content module created successfully.');
    }
}
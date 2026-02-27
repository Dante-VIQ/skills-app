@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold mb-6">📚 Learning Resources</h1>
    
    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <select name="topic" class="border rounded px-3 py-2">
                <option value="">All Topics</option>
                <option value="html" {{ $topic == 'html' ? 'selected' : '' }}>HTML</option>
                <option value="css" {{ $topic == 'css' ? 'selected' : '' }}>CSS</option>
                <option value="javascript" {{ $topic == 'javascript' ? 'selected' : '' }}>JavaScript</option>
                <option value="python" {{ $topic == 'python' ? 'selected' : '' }}>Python</option>
                <option value="math" {{ $topic == 'math' ? 'selected' : '' }}>Math</option>
                <option value="science" {{ $topic == 'science' ? 'selected' : '' }}>Science</option>
            </select>
            
            <select name="difficulty" class="border rounded px-3 py-2">
                <option value="">All Levels</option>
                <option value="beginner" {{ $difficulty == 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="intermediate" {{ $difficulty == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="advanced" {{ $difficulty == 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Filter
            </button>
        </form>
    </div>
    
    {{-- Resources Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($resources as $resource)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="p-4">
                <div class="flex items-center mb-3">
                    @if($resource->source->logo_url)
                        <img src="{{ $resource->source->logo_url }}" 
                             alt="{{ $resource->source->name }}"
                             class="w-6 h-6 mr-2">
                    @endif
                    <span class="text-xs font-medium text-gray-500">
                        {{ $resource->source->name }}
                    </span>
                    <span class="ml-auto px-2 py-1 text-xs rounded 
                        @if($resource->difficulty_level == 'beginner') bg-green-100 text-green-800
                        @elseif($resource->difficulty_level == 'intermediate') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($resource->difficulty_level) }}
                    </span>
                </div>
                
                <h3 class="font-bold text-lg mb-2">{{ $resource->title }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $resource->summary }}</p>
                
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        ⏱️ {{ $resource->estimated_minutes }} min
                    </span>
                    
                    <a href="{{ route('resources.redirect', $resource->id) }}" 
                       target="_blank"
                       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700"
                       onclick="trackReferral({{ $resource->id }})">
                        Learn More →
                    </a>
                </div>
                
                {{-- Attribution --}}
                <p class="text-xs text-gray-400 mt-3">
                    {{ $resource->attribution }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
    
    {{ $resources->links() }}
</div>

@push('scripts')
<script>
function trackReferral(resourceId) {
    // Track click via AJAX
    fetch(`/api/resources/track/${resourceId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
}
</script>
@endpush
@endsection
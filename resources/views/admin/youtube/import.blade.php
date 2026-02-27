@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Import YouTube Video</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        {{-- Search Form --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Search YouTube</label>
            <div class="flex">
                <input type="text" 
                       id="search-query" 
                       placeholder="Search for educational videos..."
                       class="flex-1 border rounded-l px-4 py-2">
                <button onclick="searchVideos()" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-r hover:bg-blue-700">
                    Search
                </button>
            </div>
        </div>
        
        {{-- Results --}}
        <div id="results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Results will appear here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
async function searchVideos() {
    const query = document.getElementById('search-query').value;
    if (!query) return;
    
    const response = await fetch(`/admin/youtube/search?query=${encodeURIComponent(query)}`);
    const data = await response.json();
    
    displayResults(data.results);
}

function displayResults(results) {
    const container = document.getElementById('results');
    container.innerHTML = '';
    
    results.forEach(item => {
        const videoId = item.id.videoId;
        const snippet = item.snippet;
        
        const card = document.createElement('div');
        card.className = 'border rounded-lg overflow-hidden shadow hover:shadow-lg transition';
        card.innerHTML = `
            <img src="${snippet.thumbnails.high.url}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="font-bold mb-2">${snippet.title}</h3>
                <p class="text-sm text-gray-600 mb-2">${snippet.channelTitle}</p>
                <p class="text-xs text-gray-500 mb-4">${snippet.description.substring(0, 100)}...</p>
                <button onclick="previewVideo('${videoId}')" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Preview & Import
                </button>
            </div>
        `;
        container.appendChild(card);
    });
}

async function previewVideo(videoId) {
    const response = await fetch(`/admin/youtube/preview/${videoId}`);
    const data = await response.json();
    
    if (data.success) {
        // Show preview modal or navigate to import page
        window.location.href = `/admin/youtube/import/${videoId}`;
    } else {
        alert('Error: ' + data.message);
    }
}
</script>
@endpush
@endsection
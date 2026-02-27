@props(['item'])

<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1">
    {{-- Image --}}
    @if(isset($item['thumbnail']))
        <img src="{{ $item['thumbnail'] }}"
             class="w-full h-40 object-cover"
             alt="{{ $item['title'] }}">
    @else
        <div class="w-full h-40 bg-gradient-to-br from-blue-500 to-purple-600
                    flex items-center justify-center text-white text-4xl">
            {{ substr($item['title'], 0, 1) }}
        </div>
    @endif

    {{-- Content --}}
    <div class="p-4">
        {{-- Source & Difficulty --}}
        <div class="flex items-center mb-2">
            <span class="text-sm mr-1">{{ $item['source_icon'] ?? '📚' }}</span>
            <span class="text-xs text-gray-500">{{ $item['source'] }}</span>

            @if(isset($item['difficulty']))
                <span class="ml-auto text-xs px-2 py-1 rounded
                    @if($item['difficulty'] == 'beginner') bg-green-100 text-green-700
                    @elseif($item['difficulty'] == 'intermediate') bg-yellow-100 text-yellow-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($item['difficulty']) }}
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="font-bold text-base mb-2 line-clamp-2">
            {{ $item['title'] }}
        </h3>

        {{-- Description --}}
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
            {{ $item['description'] ?? '' }}
        </p>

        {{-- Footer --}}
        <div class="flex items-center justify-between">
            @if(isset($item['reason']))
                <span class="text-xs text-gray-400 italic">
                    {{ $item['reason'] }}
                </span>
            @else
                <span class="text-xs text-gray-400">
                    Match: {{ $item['final_score'] ?? 70 }}%
                </span>
            @endif

            <a href="{{ $item['url'] }}"
               target="{{ $item['type'] === 'internal' ? '_self' : '_blank' }}"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Learn More →
            </a>
        </div>
    </div>
</div>

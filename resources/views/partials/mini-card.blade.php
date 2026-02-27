@props(['item'])

<a href="{{ $item['url'] }}"
   target="{{ $item['type'] === 'internal' ? '_self' : '_blank' }}"
   class="bg-white rounded-lg shadow p-2 hover:shadow-md transition block">
    @if(isset($item['thumbnail']))
        <img src="{{ $item['thumbnail'] }}"
             class="w-full h-16 object-cover rounded mb-2">
    @endif
    <p class="text-xs font-medium truncate">{{ $item['title'] }}</p>
    <p class="text-xs text-gray-400">{{ $item['source'] }}</p>
</a>

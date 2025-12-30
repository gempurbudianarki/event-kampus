<div class="p-4">
    @if($record->image)
        <img src="{{ asset('storage/' . $record->image) }}" class="w-full h-48 object-cover rounded-xl mb-4">
    @endif

    <h2 class="text-xl font-bold mb-2">{{ $record->title }}</h2>
    
    <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
        {!! $record->description !!}
    </div>
</div>
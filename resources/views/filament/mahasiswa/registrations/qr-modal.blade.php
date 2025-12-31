<div class="flex flex-col items-center justify-center p-6 space-y-4 text-center">
    <div class="p-4 bg-white rounded-lg shadow-md">
        {{-- Generate QR Code on the fly --}}
        {!! QrCode::size(200)->generate($record->ticket_code) !!}
    </div>
    
    <div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $record->ticket_code }}</h2>
        <p class="text-sm text-gray-500">Tunjukkan QR ini ke panitia saat masuk.</p>
    </div>

    <div class="w-full pt-4 border-t">
        <p class="text-xs text-gray-400">Event: {{ $record->event->title }}</p>
        <p class="text-xs text-gray-400">Lokasi: {{ $record->event->location }}</p>
    </div>
</div>
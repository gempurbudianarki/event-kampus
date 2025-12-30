<div class="group relative bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 ease-out hover:-translate-y-2 border border-gray-100 dark:border-gray-800 h-full flex flex-col">
    
    <div class="relative h-64 overflow-hidden">
        @if($getRecord()->image)
            <img src="{{ asset('storage/' . $getRecord()->image) }}" 
                 alt="{{ $getRecord()->title }}" 
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 group-hover:rotate-1"
                 loading="lazy">
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500">
                No Image
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>

        <div class="absolute top-4 right-4">
            @if($getRecord()->price == 0)
                <span class="px-4 py-1.5 bg-emerald-500/90 backdrop-blur-md text-white text-xs font-bold rounded-full shadow-lg border border-emerald-400">
                    GRATIS
                </span>
            @else
                <span class="px-4 py-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-full shadow-lg">
                    Rp {{ number_format($getRecord()->price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        <div class="absolute top-4 left-4 flex flex-col items-center bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-xl p-2 shadow-lg min-w-[50px]">
            <span class="text-xs font-bold text-red-500 uppercase">{{ $getRecord()->event_date->format('M') }}</span>
            <span class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $getRecord()->event_date->format('d') }}</span>
        </div>
    </div>

    <div class="p-6 flex flex-col flex-grow relative">
        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors">
            {{ $getRecord()->title }}
        </h3>

        <div class="space-y-2 mb-4">
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $getRecord()->event_date->format('H:i') }} WIB
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="truncate">{{ $getRecord()->location }}</span>
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Sisa Kuota: <span class="font-bold ml-1 {{ $getRecord()->quota < 5 ? 'text-red-500' : 'text-gray-700 dark:text-gray-200' }}">{{ $getRecord()->quota }}</span>
            </div>
        </div>
        
        <div class="flex-grow"></div>
    </div>
</div>
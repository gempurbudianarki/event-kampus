<div class="group relative bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 ease-out hover:-translate-y-2 border border-gray-100 dark:border-gray-800 h-full flex flex-col">
    
    <div class="relative h-64 overflow-hidden">
        <div class="absolute inset-0 bg-gray-200 animate-pulse"></div> <img src="{{ asset('storage/' . $getRecord()->image) }}" 
             alt="{{ $getRecord()->title }}" 
             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 group-hover:rotate-1"
             loading="lazy">

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
        <div class="absolute top-0 left-6 right-6 h-0.5 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors">
            {{ $getRecord()->title }}
        </h3>

        <div class="space-y-2 mb-4">
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <x-heroicon-m-clock class="w-4 h-4 mr-2 text-indigo-500"/>
                {{ $getRecord()->event_date->format('H:i') }} WIB
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <x-heroicon-m-map-pin class="w-4 h-4 mr-2 text-pink-500"/>
                <span class="truncate">{{ $getRecord()->location }}</span>
            </div>
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <x-heroicon-m-user-group class="w-4 h-4 mr-2 text-emerald-500"/>
                Sisa Kuota: <span class="font-bold ml-1 {{ $getRecord()->quota < 5 ? 'text-red-500' : 'text-gray-700 dark:text-gray-200' }}">{{ $getRecord()->quota }}</span>
            </div>
        </div>

        <div class="flex-grow"></div>
    </div>
</div>
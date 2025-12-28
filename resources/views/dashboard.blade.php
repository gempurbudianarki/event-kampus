<x-admin-layout>
    <x-slot name="header">
        Statistik Kampus
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Total Event</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $events->count() }}</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Event Tayang</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $events->where('status', 'published')->count() }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-full text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Draft / Konsep</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $events->where('status', 'draft')->count() }}</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-full text-yellow-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase">Peserta</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">1,240</p>
            </div>
            <div class="p-3 bg-red-50 rounded-full text-red-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Acara Kampus</h3>
            <a href="{{ route('events.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                + Tambah Event
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Poster</th>
                        <th class="px-6 py-4 font-semibold">Info Event</th>
                        <th class="px-6 py-4 font-semibold">Waktu</th>
                        <th class="px-6 py-4 font-semibold">Kuota</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                <img src="{{ asset('storage/' . $event->banner) }}" class="w-full h-full object-cover" alt="Banner">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800 text-sm">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($event->location, 20) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700 font-medium">{{ $event->event_date->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $event->event_date->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-bold text-blue-600 bg-blue-100 rounded-full">
                                {{ $event->quota }} Kursi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($event->status == 'published')
                                <span class="px-3 py-1 text-xs font-bold text-green-600 bg-green-100 rounded-full border border-green-200">
                                    Tayang
                                </span>
                            @elseif($event->status == 'draft')
                                <span class="px-3 py-1 text-xs font-bold text-gray-600 bg-gray-200 rounded-full">
                                    Draft
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold text-red-600 bg-red-100 rounded-full">
                                    Tutup
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-indigo-600 font-medium text-sm transition mr-3">
                                Edit
                            </button>
                            <button class="text-gray-400 hover:text-red-600 font-medium text-sm transition">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Belum ada event. Klik tombol <b>Tambah Event</b> di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
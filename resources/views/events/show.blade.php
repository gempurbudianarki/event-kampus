<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->name }} - Event Kampus</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#ec4899',
                        dark: '#0f172a',
                        card: '#1e293b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-gray-200 antialiased">

    <nav class="absolute w-full z-50 py-6">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white hover:text-primary transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-bold">Kembali</span>
            </a>
        </div>
    </nav>

    <main class="min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="rounded-2xl overflow-hidden shadow-2xl border border-gray-700 relative group">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://placehold.co/600x800/1e293b/FFF?text=Poster+Event' }}" 
                                 alt="{{ $event->name }}" 
                                 class="w-full h-auto object-cover group-hover:scale-105 transition duration-700">
                            
                            <div class="absolute top-4 right-4">
                                @if($event->price == 0)
                                    <span class="bg-green-500 text-white font-bold px-4 py-2 rounded-full shadow-lg">GRATIS</span>
                                @else
                                    <span class="bg-gradient-to-r from-primary to-secondary text-white font-bold px-4 py-2 rounded-full shadow-lg">
                                        Rp {{ number_format($event->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 space-y-4 bg-card p-6 rounded-2xl border border-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-700 pb-4">
                                <span class="text-gray-400">Sisa Kuota</span>
                                <span class="font-bold text-xl {{ $event->quota > 5 ? 'text-primary' : 'text-red-500' }}">
                                    {{ $event->quota }} Kursi
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Tanggal</span>
                                <span class="font-medium text-white">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Jam</span>
                                <span class="font-medium text-white">{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                        {{ $event->name }}
                    </h1>

                    <div class="flex items-center gap-4 text-gray-400 mb-8 text-sm md:text-base">
                        <div class="flex items-center gap-2 bg-gray-800 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $event->location }}
                        </div>
                    </div>

                    <div class="prose prose-invert prose-lg max-w-none text-gray-300 mb-12">
                        {!! $event->description !!}
                    </div>

                    <div class="border-t border-gray-800 pt-8">
                        @if(session('success'))
                            <div class="bg-green-500/10 border border-green-500 text-green-400 p-4 rounded-xl mb-4 text-center font-bold">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl mb-4 text-center font-bold">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="bg-yellow-500/10 border border-yellow-500 text-yellow-400 p-4 rounded-xl mb-4 text-center font-bold">
                                {{ session('warning') }}
                            </div>
                        @endif

                        @auth
                            @if($event->quota > 0)
                                <form action="{{ route('event.register', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin mendaftar event ini?')"
                                        class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white font-bold text-lg rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:scale-105 flex items-center justify-center gap-2">
                                        🚀 Daftar Sekarang
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full md:w-auto px-8 py-4 bg-gray-700 text-gray-400 font-bold text-lg rounded-xl cursor-not-allowed">
                                    🚫 Kuota Habis
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full md:w-auto text-center px-8 py-4 bg-gray-800 border border-gray-700 hover:bg-white hover:text-dark text-white font-bold text-lg rounded-xl transition">
                                🔒 Login untuk Daftar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
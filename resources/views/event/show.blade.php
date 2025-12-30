<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Detail Event</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',   // Indigo
                        secondary: '#ec4899', // Pink
                        dark: '#0f172a',      // Slate 900
                        card: '#1e293b',      // Slate 800
                    }
                }
            }
        }
    </script>

    <style>
        /* Glass Effect Navbar */
        .glass-nav {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }
    </style>
</head>
<body class="bg-[#020617] text-gray-200 antialiased selection:bg-primary selection:text-white">

    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="group flex items-center justify-center w-10 h-10 rounded-full bg-gray-800 border border-gray-700 hover:border-primary text-white transition-all hover:scale-110">
                        <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div class="hidden md:block">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Event Kampus</p>
                        <h1 class="font-bold text-white text-lg truncate max-w-md">{{ $event->title }}</h1>
                    </div>
                </div>

                @auth
                    <div class="flex items-center gap-3 bg-gray-800/50 py-1.5 px-4 rounded-full border border-gray-700">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2 space-y-8 animate-fade-in-up">
                
                <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-primary/10 border border-gray-800 group aspect-video bg-gray-900">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="{{ $event->title }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-600">
                            <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-medium">Gambar Event Tidak Tersedia</span>
                        </div>
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-transparent to-transparent"></div>

                    <div class="absolute top-4 left-4 bg-gray-900/90 backdrop-blur-md px-4 py-2 rounded-2xl border border-gray-700 shadow-xl">
                        <span class="block text-2xl font-extrabold text-primary text-center leading-none">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                        </span>
                        <span class="block text-xs font-bold text-gray-300 uppercase tracking-wider text-center mt-1">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('M Y') }}
                        </span>
                    </div>
                </div>

                <div>
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                        {{ $event->title }}
                    </h1>
                    
                    <div class="flex flex-wrap gap-3 mb-8">
                        <div class="flex items-center gap-2 bg-card px-4 py-2.5 rounded-xl border border-gray-700 text-gray-300">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB</span>
                        </div>
                        
                        <div class="flex items-center gap-2 bg-card px-4 py-2.5 rounded-xl border border-gray-700 text-gray-300">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="font-medium">{{ $event->location }}</span>
                        </div>

                        <div class="flex items-center gap-2 bg-card px-4 py-2.5 rounded-xl border border-gray-700 text-gray-300">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-medium">Sisa Kuota: <span class="text-white font-bold">{{ $event->quota }}</span></span>
                        </div>
                    </div>

                    <div class="p-6 bg-card rounded-3xl border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-gradient-to-b from-primary to-secondary rounded-full"></span>
                            Deskripsi Lengkap
                        </h3>
                        <div class="prose prose-invert prose-lg max-w-none text-gray-400 leading-relaxed">
                            {!! $event->description !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-6">
                    
                    <div class="bg-card border border-gray-700 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 text-center mb-6">
                            <p class="text-gray-400 text-sm mb-2 uppercase tracking-wider font-semibold">Harga Tiket Masuk</p>
                            @if($event->price == 0)
                                <h2 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500">GRATIS</h2>
                            @else
                                <h2 class="text-4xl font-extrabold text-white">Rp {{ number_format($event->price, 0, ',', '.') }}</h2>
                            @endif
                        </div>

                        <div class="h-px w-full bg-gray-700 mb-6"></div>

                        @if(session('error'))
                            <div class="bg-red-500/10 border border-red-500/50 text-red-200 p-4 rounded-xl text-center mb-6 animate-pulse">
                                <p class="font-bold text-sm">{{ session('error') }}</p>
                            </div>
                        @endif
                        
                        @if(session('warning'))
                             <div class="bg-yellow-500/10 border border-yellow-500/50 text-yellow-200 p-4 rounded-xl text-center mb-6">
                                <p class="font-bold text-sm">{{ session('warning') }}</p>
                            </div>
                        @endif


                        @auth
                            @if($isRegistered)
                                <div class="text-center bg-green-500/10 border border-green-500/50 rounded-2xl p-5 mb-4">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-green-500/30">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-green-400 font-bold text-lg">Kamu Sudah Terdaftar!</p>
                                    <p class="text-green-200/70 text-sm mt-1">Tiket elektronik sudah tersedia di dashboard.</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full py-4 rounded-xl bg-gray-700 hover:bg-gray-600 text-white font-bold transition border border-gray-600 group">
                                    Lihat Tiket Saya
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>

                            @elseif($event->quota <= 0)
                                <div class="text-center bg-red-500/10 border border-red-500/50 rounded-2xl p-5">
                                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-red-500/30">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <p class="text-red-400 font-bold text-lg">Mohon Maaf, Kuota Habis!</p>
                                    <p class="text-red-200/70 text-sm mt-1">Nantikan event menarik lainnya.</p>
                                </div>

                            @else
                                <form action="{{ route('event.register', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white font-bold text-lg shadow-xl shadow-primary/30 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2" onclick="return confirm('Apakah Anda yakin ingin mendaftar event ini? Pastikan data diri sudah benar.')">
                                        <span>DAFTAR SEKARANG</span>
                                        <svg class="w-5 h-5 animate-bounce-x" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </button>
                                </form>
                                <p class="text-center text-gray-500 text-xs mt-4 px-4">
                                    Dengan menekan tombol daftar, kamu menyetujui syarat & ketentuan yang berlaku.
                                </p>
                            @endif
                        @else
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <p class="text-gray-300 font-medium mb-4">Silakan login terlebih dahulu untuk mendaftar event ini.</p>
                                
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}" class="block w-full py-3 rounded-xl bg-white text-dark font-bold text-center hover:bg-gray-200 transition shadow-lg shadow-white/10">
                                        Login Akun
                                    </a>
                                    <a href="{{ route('register') }}" class="block w-full py-3 rounded-xl border border-gray-600 text-gray-300 font-semibold text-center hover:border-primary hover:text-primary transition">
                                        Daftar Akun Baru
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <div class="bg-card/50 border border-gray-800 rounded-3xl p-6 text-center">
                        <p class="text-gray-400 text-sm mb-2">Butuh Bantuan?</p>
                        <a href="#" class="text-primary hover:text-secondary font-medium transition text-sm flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Hubungi Admin UBBG
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>

</body>
</html>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Kampus UBBG - Kegiatan Mahasiswa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',   // Indigo Primary
                        secondary: '#ec4899', // Pink Accent
                        dark: '#0f172a',      // Slate 900
                        darker: '#020617',    // Slate 950
                        card: '#1e293b',      // Slate 800
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }

        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Card Hover Effect */
        .event-card:hover .event-image {
            transform: scale(1.1);
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }
    </style>
</head>
<body class="bg-darker text-gray-200 antialiased overflow-x-hidden selection:bg-primary selection:text-white">

    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary/30">
                        E
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-white">Event<span class="text-primary">Kampus</span></span>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <span class="hidden md:block text-gray-400 text-sm font-medium">Hai, <span class="text-white">{{ Auth::user()->name }}</span></span>
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-card border border-gray-700 hover:border-primary text-white font-medium transition shadow-lg flex items-center gap-2 group">
                                <span>Dashboard</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white font-medium transition px-2">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white font-bold shadow-lg shadow-indigo-500/30 transition transform hover:scale-105">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-40 pb-24 lg:pt-52 lg:pb-40 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full z-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-96 h-96 bg-primary/20 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-secondary/10 rounded-full blur-[150px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 text-center">
            <div data-aos="fade-down" class="inline-flex items-center gap-2 py-1 px-4 rounded-full bg-gray-800/50 border border-gray-700 text-primary text-sm font-semibold mb-8 backdrop-blur-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                Portal Resmi Kegiatan UBBG
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 text-white leading-tight" data-aos="fade-up">
                Eksplorasi Event <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-purple-400 to-secondary text-glow">Tanpa Batas</span>
            </h1>

            <p class="mt-6 text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                Temukan seminar, workshop, dan kompetisi coding terbaru. Kembangkan skill, perluas relasi, dan raih prestasi.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                <a href="#events" class="px-8 py-4 rounded-full bg-white text-dark font-bold hover:bg-gray-100 transition shadow-xl hover:shadow-2xl hover:-translate-y-1">
                    Cari Event Sekarang 🚀
                </a>
            </div>
        </div>
    </section>

    <section id="events" class="py-24 bg-dark relative border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Event Terbaru</h2>
                <div class="h-1 w-24 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $index => $event)
                        <div class="event-card group relative bg-card rounded-2xl overflow-hidden border border-gray-800 hover:border-primary/50 transition duration-500 h-full flex flex-col shadow-lg hover:shadow-primary/10" 
                             data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            
                            <div class="relative h-56 overflow-hidden">
                                @if($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="event-image w-full h-full object-cover transition duration-700 ease-in-out">
                                @else
                                    <img src="https://placehold.co/600x400/1e293b/FFF?text=Event+UBBG" class="event-image w-full h-full object-cover transition duration-700 ease-in-out">
                                @endif
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-card via-transparent to-transparent opacity-90"></div>
                                
                                <div class="absolute top-4 left-4 bg-gray-900/80 backdrop-blur border border-gray-600 text-white px-3 py-1.5 rounded-lg text-center shadow-lg">
                                    <span class="block text-xs text-gray-400 uppercase font-bold tracking-wider">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M') }}
                                    </span>
                                    <span class="block text-xl font-bold text-primary">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                    </span>
                                </div>

                                <div class="absolute top-4 right-4">
                                    @if($event->price == 0)
                                        <span class="bg-green-500/90 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg backdrop-blur-sm">
                                            GRATIS
                                        </span>
                                    @else
                                        <span class="bg-gradient-to-r from-primary to-secondary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                                            Rp {{ number_format($event->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-white mb-3 line-clamp-2 group-hover:text-primary transition duration-300">
                                    {{ $event->title }} 
                                </h3>
                                
                                <div class="flex items-center text-sm text-gray-400 mb-4 gap-4">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ Str::limit($event->location, 15) }}
                                    </div>
                                </div>

                                <div class="text-gray-500 text-sm mb-6 line-clamp-2">
                                    {!! strip_tags($event->description) !!}
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-700/50">
                                    <a href="{{ route('event.show', $event->id) }}" class="block w-full py-3 rounded-xl bg-gray-800 hover:bg-primary text-white text-center font-semibold transition duration-300 group-hover:shadow-lg group-hover:shadow-primary/25">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 text-center bg-gray-800/20 rounded-3xl border border-dashed border-gray-700" data-aos="zoom-in">
                    <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Belum Ada Event</h3>
                    <p class="text-gray-400">Pantau terus ya! Event seru akan segera hadir.</p>
                </div>
            @endif
        </div>
    </section>

    <footer class="bg-black py-10 border-t border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Event<span class="text-primary">Kampus</span></h2>
            <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} Gempur Budi Anarki. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });
    </script>
</body>
</html>
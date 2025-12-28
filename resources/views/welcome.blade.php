<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Kampus - Platform Kegiatan Mahasiswa</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                        primary: '#6366f1', // Warna Utama (Indigo)
                        secondary: '#ec4899', // Warna Aksen (Pink)
                        dark: '#0f172a', // Background Gelap
                        card: '#1e293b', // Background Kartu
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }

        /* Glass Effect */
        .glass-nav {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-dark text-gray-200 antialiased overflow-x-hidden selection:bg-primary selection:text-white">

    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        E
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-white">Event<span class="text-primary">Kampus</span></span>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <span class="hidden md:block text-gray-400 text-sm">Halo, {{ Auth::user()->name }}</span>
                        <a href="{{ url('/mahasiswa') }}" class="px-6 py-2.5 rounded-full bg-gray-800 border border-gray-700 hover:border-primary text-white font-medium transition shadow-lg flex items-center gap-2">
                            <span>Dashboard Saya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ url('/mahasiswa/login') }}" class="text-gray-300 hover:text-white font-medium transition">Masuk</a>
                        
                        <a href="{{ url('/mahasiswa/register') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-primary to-secondary hover:opacity-90 text-white font-bold shadow-lg shadow-indigo-500/30 transition transform hover:scale-105">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full z-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-primary/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-gray-800/50 border border-gray-700 text-primary text-sm font-semibold mb-6 animate-pulse">
                🔥 Platform Kegiatan Mahasiswa Terupdate
            </span>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 text-white" data-aos="fade-up">
                Temukan Event <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Kampus Impianmu</span>
            </h1>
            <p class="mt-4 text-xl text-gray-400 max-w-2xl mx-auto mb-10" data-aos="fade-up" data-aos-delay="100">
                Jangan lewatkan seminar, workshop, dan kompetisi seru. Daftar mudah, tiket aman, pengalaman tak terlupakan.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                @auth
                    <a href="#events" class="px-8 py-4 rounded-full bg-white text-dark font-bold hover:bg-gray-200 transition shadow-xl">
                        Lihat Semua Event 👇
                    </a>
                @else
                    <a href="{{ url('/mahasiswa/register') }}" class="px-8 py-4 rounded-full bg-gradient-to-r from-primary to-secondary text-white font-bold hover:opacity-90 transition shadow-lg shadow-indigo-500/40">
                        Buat Akun Gratis
                    </a>
                    <a href="#events" class="px-8 py-4 rounded-full bg-gray-800 text-white font-bold border border-gray-700 hover:bg-gray-700 transition">
                        Lihat Event Dulu
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <section id="events" class="py-20 bg-dark relative border-t border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12" data-aos="fade-right">
                <div>
                    <h2 class="text-3xl font-bold text-white">Event Terbaru</h2>
                    <p class="text-gray-400 mt-2">Pilih kegiatan yang sesuai minatmu</p>
                </div>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $index => $event)
                        <div class="group relative bg-card rounded-2xl overflow-hidden border border-gray-800 hover:border-primary/50 transition duration-300 h-full flex flex-col" 
                             data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            
                            <div class="relative h-52 overflow-hidden bg-gray-700">
                                <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://placehold.co/600x400/1e293b/FFF?text=No+Image' }}" 
                                     alt="{{ $event->name }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-card via-transparent to-transparent opacity-90"></div>
                                
                                <div class="absolute top-4 right-4 bg-gray-900/90 backdrop-blur border border-gray-600 text-white px-3 py-1 rounded-lg text-center shadow-lg">
                                    <span class="block text-xs text-gray-400 uppercase font-bold">
                                        {{ \Carbon\Carbon::parse($event->date)->format('M') }}
                                    </span>
                                    <span class="block text-xl font-bold text-primary">
                                        {{ \Carbon\Carbon::parse($event->date)->format('d') }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-white mb-2 line-clamp-2 group-hover:text-primary transition">
                                    {{ $event->name }}
                                </h3>
                                <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-1">
                                    {{ $event->description ?? 'Deskripsi belum tersedia.' }}
                                </p>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-6">
                                    <svg class="w-4 h-4 mr-2 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ Str::limit($event->location ?? 'Kampus Utama', 20) }}
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-700">
                                    @auth
                                        <a href="{{ url('/mahasiswa') }}" class="block w-full text-center bg-gray-700 hover:bg-white hover:text-dark text-white font-bold py-2 rounded-lg transition">
                                            Lihat Detail
                                        </a>
                                    @else
                                        <a href="{{ url('/mahasiswa/login') }}" class="block w-full text-center bg-primary hover:bg-indigo-500 text-white font-bold py-2 rounded-lg transition shadow-lg shadow-indigo-500/20">
                                            Login untuk Daftar
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-gray-800/30 rounded-3xl border border-dashed border-gray-700" data-aos="zoom-in">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800 mb-6">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Belum Ada Event Aktif</h3>
                    <p class="text-gray-400 mt-2">Tunggu update selanjutnya dari Admin ya!</p>
                </div>
            @endif
        </div>
    </section>

    <footer class="bg-black py-8 border-t border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500">&copy; {{ date('Y') }} EventKampus. Developed by IT Team.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
</body>
</html>
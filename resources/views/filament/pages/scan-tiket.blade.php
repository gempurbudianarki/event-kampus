<x-filament-panels::page>
    {{-- 1. Load Library Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    {{-- Layout Utama --}}
    <div x-data="qrScanner()" x-init="init()" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- BAGIAN KIRI: LAYAR KAMERA (Lebar Terkendali) --}}
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-4">
            
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 relative">
                
                {{-- Header Status --}}
                <div class="absolute top-0 left-0 right-0 z-20 p-4 flex justify-between items-center bg-gradient-to-b from-black/60 to-transparent">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" 
                             :class="isScanning ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
                        <span class="text-white text-sm font-bold shadow-sm" x-text="isScanning ? 'LIVE SCANNING' : 'KAMERA OFF'"></span>
                    </div>
                    
                    {{-- Select Kamera (Floating) --}}
                    <select x-model="selectedCamera" 
                            class="bg-black/50 text-white text-xs border-none rounded-full px-3 py-1 focus:ring-0 cursor-pointer hover:bg-black/70 transition">
                        <option value="" disabled>Pilih Kamera...</option>
                        <template x-for="cam in cameras" :key="cam.id">
                            <option :value="cam.id" x-text="cam.label || 'Kamera ' + ($index + 1)"></option>
                        </template>
                    </select>
                </div>

                {{-- AREA VIDEO (Square Ratio biar rapi) --}}
                <div class="relative w-full bg-black aspect-square md:aspect-[4/3] group">
                    
                    {{-- Element Video Library --}}
                    <div id="reader" wire:ignore class="w-full h-full"></div>
                    
                    {{-- VIEWFINDER (KOTAK FOKUS) --}}
                    <div x-show="isScanning" class="absolute inset-0 pointer-events-none z-10 flex items-center justify-center">
                        {{-- Overlay Gelap di sekeliling --}}
                        <div class="absolute inset-0 bg-black/40 mask-scanner"></div>
                        
                        {{-- Kotak Fokus --}}
                        <div class="relative w-64 h-64 border-2 border-blue-400/50 rounded-lg shadow-[0_0_0_9999px_rgba(0,0,0,0.4)]">
                            {{-- Sudut-sudut Penanda --}}
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-blue-500 -mt-1 -ml-1 rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-blue-500 -mt-1 -mr-1 rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-blue-500 -mb-1 -ml-1 rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-blue-500 -mb-1 -mr-1 rounded-br-lg"></div>
                            
                            {{-- Garis Scan Bergerak --}}
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.8)] animate-scan-line"></div>
                        </div>

                        {{-- Instruksi Teks --}}
                        <div class="absolute bottom-10 left-0 right-0 text-center">
                            <p class="text-white text-sm font-medium bg-black/50 inline-block px-4 py-1 rounded-full">
                                Arahkan QR Code ke dalam kotak
                            </p>
                        </div>
                    </div>

                    {{-- Pesan Error / Tombol Start Awal --}}
                    <div x-show="!isScanning && !errorMessage" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-10">
                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-4 text-4xl animate-bounce">
                            📷
                        </div>
                        <button @click="startScan()" 
                                :disabled="!selectedCamera"
                                class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-full shadow-lg hover:shadow-blue-500/50 transition transform hover:scale-105 disabled:opacity-50 disabled:scale-100">
                            MULAI SCAN
                        </button>
                        <p class="text-gray-500 text-sm mt-4">Pastikan browser mengizinkan akses kamera</p>
                    </div>

                     {{-- Pesan Error --}}
                     <div x-show="errorMessage" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-20 p-6 text-center">
                        <div class="text-5xl mb-4">🚫</div>
                        <h3 class="text-white font-bold text-lg">Akses Kamera Bermasalah</h3>
                        <p class="text-gray-400 text-sm mt-2 mb-6" x-text="errorMessage"></p>
                        <button @click="initCamera()" class="px-4 py-2 border border-white text-white rounded-lg hover:bg-white hover:text-black transition">
                            Coba Muat Ulang
                        </button>
                    </div>
                </div>

                {{-- Footer Kontrol --}}
                <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <p class="text-xs text-gray-500">Scanner Pro v2.0</p>
                    <button x-show="isScanning" @click="stopScan()" 
                            class="text-red-600 hover:text-red-700 text-sm font-bold flex items-center gap-1 bg-red-50 dark:bg-red-900/20 px-3 py-1 rounded-lg transition">
                        ⏹ STOP KAMERA
                    </button>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: DATA PESERTA (Sidebar) --}}
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 h-full flex flex-col">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Hasil Verifikasi</h3>
                    <p class="text-sm text-gray-500">Data scan terakhir akan muncul di sini.</p>
                </div>
                
                <div class="flex-1 p-6 flex items-center justify-center bg-gray-50 dark:bg-gray-800/50">
                    @if($scannedData)
                        <div class="w-full text-center animate-fade-in-up">
                            {{-- Status Icon --}}
                            <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center text-5xl mb-6 shadow-lg 
                                {{ $scannedData['status'] == 'Check-in Berhasil' ? 'bg-green-100 text-green-600' : 
                                  ($scannedData['status'] == 'Sudah Check-in' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
                                {{ $scannedData['status'] == 'Check-in Berhasil' ? '✅' : ($scannedData['status'] == 'Sudah Check-in' ? '⚠️' : '⛔') }}
                            </div>

                            <h2 class="text-2xl font-black mb-1 {{ $scannedData['color'] }}">
                                {{ $scannedData['status'] }}
                            </h2>
                            <p class="text-xs text-gray-400 font-mono mb-6">{{ now()->format('H:i:s') }}</p>

                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 text-left space-y-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Nama Peserta</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $scannedData['name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Event</p>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400 line-clamp-2">{{ $scannedData['event'] }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-400">
                            <div class="inline-block p-4 rounded-full bg-gray-200 dark:bg-gray-700 mb-4 animate-pulse">
                                🔍
                            </div>
                            <p class="font-medium">Menunggu Scan...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT LOGIC --}}
    <script>
        function qrScanner() {
            return {
                html5QrCode: null,
                isScanning: false,
                cameras: [],
                selectedCamera: '',
                errorMessage: null,
                lastResult: null,

                init() {
                    // Audio Listener
                    window.addEventListener('play-sound', event => {
                        let src = event.detail.status === 'success' 
                            ? 'https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3' 
                            : 'https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3';
                        new Audio(src).play();
                    });
                    this.initCamera();
                },

                initCamera() {
                    this.errorMessage = null;
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            this.cameras = devices;
                            // Prefer kamera belakang (environment)
                            let backCam = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('belakang'));
                            this.selectedCamera = backCam ? backCam.id : devices[devices.length - 1].id;
                        } else {
                            this.errorMessage = "Tidak ada kamera ditemukan.";
                        }
                    }).catch(err => {
                        this.errorMessage = "Izin kamera ditolak / Wajib HTTPS.";
                    });
                },

                startScan() {
                    if (!this.selectedCamera) return;
                    
                    // Reset UI Video biar bersih
                    document.getElementById('reader').innerHTML = "";
                    
                    this.html5QrCode = new Html5Qrcode("reader");
                    
                    // Config agar video fit ke container (Cover)
                    const config = { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0 
                    };
                    
                    this.html5QrCode.start(
                        this.selectedCamera, 
                        config,
                        (decodedText) => {
                            // Logic Debounce (Biar gak scan berkali-kali)
                            if (this.lastResult !== decodedText) {
                                this.lastResult = decodedText;
                                $wire.checkTicket(decodedText);
                                
                                // Jeda 3 detik sebelum bisa scan lagi
                                setTimeout(() => { this.lastResult = null; }, 3000);
                            }
                        },
                        (error) => {} // Abaikan error frame kosong
                    ).then(() => {
                        this.isScanning = true;
                        this.errorMessage = null;
                        this.fixVideoStyles();
                    }).catch(err => {
                        this.errorMessage = "Gagal Start: " + err;
                        this.isScanning = false;
                    });
                },

                stopScan() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => {
                            this.isScanning = false;
                            this.html5QrCode.clear();
                        }).catch(console.error);
                    }
                },
                
                // Hack CSS biar video-nya 'Object-Fit: Cover' (Penuh kotak)
                fixVideoStyles() {
                    setTimeout(() => {
                        const video = document.querySelector('#reader video');
                        if(video) {
                            video.style.objectFit = 'cover';
                            video.style.width = '100%';
                            video.style.height = '100%';
                            video.style.borderRadius = '0.75rem'; // Rounded sesuai container
                        }
                    }, 500);
                }
            }
        }
    </script>

    {{-- CUSTOM CSS --}}
    <style>
        /* Animasi Garis Scan */
        @keyframes scanLine {
            0% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            animation: scanLine 2s ease-in-out infinite;
        }
        
        /* Animasi Hasil */
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Hide Element Bawaan Library yang ganggu */
        #reader__scan_region img { display: none !important; }
        #reader__dashboard_section_csr button { display: none !important; }
    </style>
</x-filament-panels::page>
<x-filament::page>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="mb-4">
        {{ $this->form }}
    </div>

    <div 
        x-data="{
            isProcessing: false,
            scanner: null,

            initScanner() {
                const onScanSuccess = (decodedText, decodedResult) => {
                    if (this.isProcessing) return;
                    this.isProcessing = true;
                    
                    console.log(`Scan Code: ${decodedText}`);

                    // Panggil Backend
                    $wire.processTicket(decodedText).then(() => {
                        // Jeda 3 detik agar suara selesai & info terbaca
                        setTimeout(() => {
                            this.isProcessing = false;
                        }, 3000);
                    }).catch(() => {
                        this.isProcessing = false;
                    });
                }

                const onScanFailure = (error) => {}

                this.$nextTick(() => {
                    if(!document.getElementById('reader')) return;
                    try {
                        this.scanner = new Html5QrcodeScanner(
                            'reader', 
                            { 
                                fps: 10, 
                                qrbox: {width: 250, height: 250},
                                rememberLastUsedCamera: true
                            },
                            false
                        );
                        this.scanner.render(onScanSuccess, onScanFailure);
                    } catch (e) {
                        console.error('Scanner Error:', e);
                    }
                });
            },

            playSound(status) {
                console.log('Playing sound for status:', status); // Debugging
                
                let audioUrl = '';
                
                if (status === 'success') {
                    // TING! (Sukses)
                    audioUrl = 'https://cdn.freesound.org/previews/171/171671_2437358-lq.mp3'; 
                } else {
                    // TETOT! (Gagal / Warning / Salah Event)
                    audioUrl = 'https://cdn.freesound.org/previews/142/142608_2437358-lq.mp3';
                }
                
                let audio = new Audio(audioUrl);
                audio.volume = 1.0; // Full volume
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        }"
        x-init="initScanner()"
        x-on:play-sound.window="playSound($event.detail.status)" 
        class="grid grid-cols-1 lg:grid-cols-2 gap-6"
    >

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center">
            
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2">
                <x-heroicon-o-camera class="w-6 h-6 text-primary-600"/>
                Kamera Scanner
            </h2>

            <div wire:ignore class="w-full">
                <div id="reader" class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-black" style="min-height: 300px;"></div>
            </div>
            
            <p class="text-sm text-gray-500 mt-4 text-center">
                Arahkan QR Code tiket ke kotak di atas.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
            
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2">
                <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-primary-600"/>
                Detail Tiket
            </h2>

            @if($scannedResult)
                <div class="flex flex-col items-center justify-center text-center h-full animate-pulse-once z-10 relative">
                    
                    @if($scannedResult['status'] === 'success')
                        <div class="rounded-full bg-green-100 p-4 mb-4">
                            <x-heroicon-o-check-circle class="w-16 h-16 text-green-600"/>
                        </div>
                        <h3 class="text-2xl font-bold text-green-700 mb-1">{{ $scannedResult['title'] }}</h3>
                        <div class="text-gray-600 dark:text-gray-300 text-lg mb-6">{!! $scannedResult['desc'] !!}</div>

                        <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-left space-y-2 border border-green-200">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kode:</span>
                                <span class="font-mono font-bold">{{ $scannedResult['data']->ticket_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Event:</span>
                                <span class="font-bold">{{ $scannedResult['data']->event->title }}</span>
                            </div>
                        </div>

                    @elseif($scannedResult['status'] === 'warning')
                        <div class="rounded-full bg-orange-100 p-4 mb-4">
                            <x-heroicon-o-exclamation-triangle class="w-16 h-16 text-orange-600"/>
                        </div>
                        <h3 class="text-2xl font-bold text-orange-700 mb-1">{{ $scannedResult['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">{!! $scannedResult['desc'] !!}</p>

                    @else
                        <div class="rounded-full bg-red-100 p-4 mb-4">
                            <x-heroicon-o-x-circle class="w-16 h-16 text-red-600"/>
                        </div>
                        <h3 class="text-2xl font-bold text-red-700 mb-1">{{ $scannedResult['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-lg font-medium">{!! $scannedResult['desc'] !!}</p>
                    @endif

                    <button wire:click="$set('scannedResult', null)" class="mt-8 px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition">
                        Scan Lagi
                    </button>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-64 text-gray-400 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                    <x-heroicon-o-qr-code class="w-20 h-20 mb-4 opacity-50"/>
                    <p class="text-lg font-medium">Siap Memindai</p>
                    <p class="text-sm">Silakan pilih event terlebih dahulu.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        #reader__scan_region { background: white; }
        #reader__dashboard_section_csr button { 
            background-color: #2563eb; color: white; padding: 8px 16px; 
            border-radius: 6px; border: none; cursor: pointer; font-weight: bold; margin-bottom: 10px;
        }
        #reader__dashboard_section_swaplink { display: none !important; }
        .animate-pulse-once { animation: pulse-green 0.3s ease-in-out; }
        @keyframes pulse-green {
            0% { transform: scale(0.95); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</x-filament::page>
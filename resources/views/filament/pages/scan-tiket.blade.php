<x-filament::page>
    <div x-data="ticketScanner()" x-init="initScanner()" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="space-y-6">
            <x-filament::section>
                <div class="mb-4">
                    {{ $this->form }}
                </div>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 dark:bg-gray-900 text-center">
                    <h3 class="text-lg font-bold mb-2">Arahkan QR ke Kamera</h3>
                    
                    <div id="reader" width="100%" style="border-radius: 8px; overflow: hidden;"></div>
                    
                    <p class="text-sm text-gray-500 mt-2">Pastikan cahaya cukup terang</p>
                </div>
            </x-filament::section>
        </div>

        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Hasil Pemindaian
                </x-slot>

                <div class="flex flex-col items-center justify-center min-h-[300px] text-center">
                    
                    @if(!$scanResult)
                        <div class="text-gray-400">
                            <x-heroicon-o-qr-code class="w-24 h-24 mx-auto mb-4 opacity-50"/>
                            <p class="text-xl">Menunggu scan...</p>
                        </div>
                    @endif

                    @if($scanResult === 'success')
                        <div class="w-full bg-green-100 dark:bg-green-900/30 border border-green-500 rounded-xl p-6 animate-pulse-once">
                            <x-heroicon-s-check-circle class="w-20 h-20 text-green-600 mx-auto mb-4"/>
                            <h2 class="text-3xl font-bold text-green-700 dark:text-green-400 mb-2">VALID</h2>
                            <p class="text-lg text-green-800 dark:text-green-300 font-semibold">{{ $scanMessage }}</p>
                            
                            <div class="mt-6 border-t border-green-200 pt-4 text-left">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Nama Peserta:</p>
                                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $participantData['name'] }}</p>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Tipe Tiket:</p>
                                <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded-full font-bold">
                                    {{ $participantData['type'] }}
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($scanResult === 'error')
                        <div class="w-full bg-red-100 dark:bg-red-900/30 border border-red-500 rounded-xl p-6">
                            <x-heroicon-s-x-circle class="w-20 h-20 text-red-600 mx-auto mb-4"/>
                            <h2 class="text-3xl font-bold text-red-700 dark:text-red-400 mb-2">DITOLAK</h2>
                            <p class="text-lg text-red-800 dark:text-red-300 font-bold">{{ $scanMessage }}</p>
                            <p class="text-sm text-gray-500 mt-4">Kode: {{ $scannedCode }}</p>
                        </div>
                    @endif

                    @if($scanResult === 'warning')
                        <div class="w-full bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-500 rounded-xl p-6">
                            <x-heroicon-s-exclamation-triangle class="w-20 h-20 text-yellow-600 mx-auto mb-4"/>
                            <h2 class="text-3xl font-bold text-yellow-700 dark:text-yellow-400 mb-2">PERHATIAN</h2>
                            <p class="text-lg text-yellow-800 dark:text-yellow-300 font-bold">{{ $scanMessage }}</p>
                        </div>
                    @endif

                </div>
            </x-filament::section>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <script>
        // Mendefinisikan Component AlpineJS
        function ticketScanner() {
            return {
                html5QrcodeScanner: null,
                
                initScanner() {
                    // Mencegah inisialisasi ganda
                    if (this.html5QrcodeScanner) return;

                    this.html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", 
                        { fps: 10, qrbox: {width: 250, height: 250} }, 
                        /* verbose= */ false
                    );

                    // Render Scanner
                    this.html5QrcodeScanner.render(
                        (decodedText) => this.onScanSuccess(decodedText), 
                        (error) => this.onScanFailure(error)
                    );
                },

                onScanSuccess(decodedText) {
                    console.log("Scanned Code:", decodedText);
                    
                    // PANGGIL BACKEND LIVEWIRE VIA $wire
                    // Ini pengganti @this yang bikin error di IDE
                    this.$wire.checkTicket(decodedText);
                    
                    // Pause scanner sebentar biar gak spam request
                    this.html5QrcodeScanner.pause();
                    
                    // Resume setelah 2.5 detik
                    setTimeout(() => {
                        this.html5QrcodeScanner.resume();
                    }, 2500); 
                },

                onScanFailure(error) {
                    // Biarkan kosong biar console gak penuh warning
                }
            }
        }
    </script>
</x-filament::page>
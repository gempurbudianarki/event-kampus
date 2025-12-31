<div class="p-4 text-center" 
     x-data="{
        isReady: false,
        isLoading: true,
        token: '{{ $record->snap_token }}',
        
        init() {
            // 1. Cek apakah script Midtrans sudah ada di browser?
            if (window.snap) {
                this.isReady = true;
                this.isLoading = false;
                console.log('Midtrans sudah siap.');
            } else {
                // 2. Kalau belum, suntikkan script secara manual
                console.log('Injecting Midtrans Script...');
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js'; // Ganti ke production URL nanti kalau live
                script.setAttribute('data-client-key', '{{ env('MIDTRANS_CLIENT_KEY') }}');
                
                script.onload = () => { 
                    this.isReady = true; 
                    this.isLoading = false;
                    console.log('Midtrans berhasil dimuat!'); 
                };
                
                script.onerror = () => {
                    alert('Gagal memuat sistem pembayaran. Cek koneksi internet.');
                    this.isLoading = false;
                };

                document.head.appendChild(script);
            }
        },

        pay() {
            if (!this.isReady) {
                alert('Sistem pembayaran belum siap. Tunggu sebentar...');
                return;
            }

            console.log('Membuka Snap untuk Token:', this.token);
            
            // 3. Eksekusi Pembayaran
            window.snap.pay(this.token, {
                onSuccess: function(result){ 
                    alert('Pembayaran Berhasil!'); 
                    window.location.reload(); 
                },
                onPending: function(result){ 
                    alert('Menunggu Pembayaran...'); 
                    window.location.reload(); 
                },
                onError: function(result){ 
                    alert('Pembayaran Gagal!'); 
                    window.location.reload(); 
                },
                onClose: function(){ 
                    console.log('Popup ditutup user');
                }
            });
        }
     }"
>
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Konfirmasi Pembayaran</h3>
        <p class="text-sm text-gray-500">Total Tagihan: <span class="font-bold">Rp {{ number_format($record->event->price, 0, ',', '.') }}</span></p>
    </div>

    {{-- Indikator Loading Script --}}
    <div x-show="isLoading" class="mb-4 text-sm text-blue-500 animate-pulse">
        Sedang menyiapkan sistem pembayaran... ⏳
    </div>

    {{-- TOMBOL BAYAR --}}
    <button type="button"
            x-show="!isLoading"
            @click="pay()"
            :disabled="!isReady"
            :class="isReady ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
            class="px-6 py-2 font-bold text-white transition rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        Bayar Sekarang 💳
    </button>
    
    {{-- Debug Info (Kalau Token Kosong) --}}
    @if(empty($record->snap_token))
        <div class="mt-4 text-xs text-red-500">
            Error: Snap Token tidak ditemukan. Coba daftar ulang.
        </div>
    @endif
</div>
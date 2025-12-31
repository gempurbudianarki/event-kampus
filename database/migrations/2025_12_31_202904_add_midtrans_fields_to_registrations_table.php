<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Token unik transaksi dari Midtrans (Kunci buat lanjut bayar)
            $table->string('snap_token')->nullable()->after('ticket_code');
            
            // ID Transaksi resmi dari Midtrans (buat pengecekan/audit)
            $table->string('midtrans_transaction_id')->nullable()->after('snap_token');
            
            // Metode pembayaran yang dipakai (Gopay, BCA, Alfamart, dll)
            $table->string('payment_method')->nullable()->after('midtrans_transaction_id');
            
            // Status khusus pembayaran (pending, paid, expire, cancel)
            // Ini beda sama status tiket ya. Status tiket itu 'confirmed/attended'.
            // Ini murni status duitnya.
            $table->string('payment_status')->default('pending')->after('status');
            
            // Waktu lunas (penting buat laporan keuangan)
            $table->dateTime('paid_at')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'midtrans_transaction_id',
                'payment_method',
                'payment_status',
                'paid_at'
            ]);
        });
    }
};
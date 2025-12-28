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
            // Tambah kolom untuk simpan nama file gambar bukti transfer
            // Nullable artinya boleh kosong (kalau event gratis)
            $table->string('payment_proof')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Kalau dimigrate:rollback, kolom ini dihapus
            $table->dropColumn('payment_proof');
        });
    }
};
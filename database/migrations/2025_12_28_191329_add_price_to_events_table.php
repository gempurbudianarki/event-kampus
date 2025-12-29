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
        Schema::table('events', function (Blueprint $table) {
            // PENGAMAN: Cek dulu apakah kolom 'price' sudah ada?
            // Kalau BELUM ada (!), baru kita buat.
            // Kalau SUDAH ada, kode ini akan dilewati (jadi gak error).
            if (!Schema::hasColumn('events', 'price')) {
                $table->decimal('price', 12, 0)->default(0)->after('quota');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Hapus kolom hanya jika kolomnya memang ada
            if (Schema::hasColumn('events', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};
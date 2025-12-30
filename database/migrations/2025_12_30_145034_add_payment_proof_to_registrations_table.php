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
            // Kita tambah kolom payment_proof (Boleh kosong/nullable) setelah kolom status
            $table->string('payment_proof')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Hapus kolom kalau migrasi dibatalkan (Rollback)
            $table->dropColumn('payment_proof');
        });
    }
};
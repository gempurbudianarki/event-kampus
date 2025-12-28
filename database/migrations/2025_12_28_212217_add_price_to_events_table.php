<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Kita tambah kolom price (Harga)
            // Default 0 artinya kalau gak diisi dianggap Gratis
            $table->decimal('price', 12, 0)->default(0)->after('quota');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
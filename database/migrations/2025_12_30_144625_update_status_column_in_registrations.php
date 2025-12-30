<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status jadi VARCHAR(50) biar muat nampung 'pending', 'confirmed', 'rejected'
        // Kita set default 'pending' juga
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Balikin ke enum kalau di-rollback (Opsional)
        // DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('confirmed', 'rejected')");
    }
};
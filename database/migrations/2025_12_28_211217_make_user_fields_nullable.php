<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita ubah jadi nullable (boleh kosong untuk Admin)
            $table->string('nim')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->string('jurusan')->nullable()->change();
            $table->string('prodi')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke wajib isi
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable(false)->change();
            $table->string('no_hp')->nullable(false)->change();
            $table->string('jurusan')->nullable(false)->change();
            $table->string('prodi')->nullable(false)->change();
        });
    }
};
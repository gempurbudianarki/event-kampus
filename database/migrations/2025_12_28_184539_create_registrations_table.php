<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            
            // Siapa yang daftar?
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Daftar event apa?
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            
            // Kode Tiket Unik
            $table->string('ticket_code')->unique();
            
            // Status Pendaftaran
            $table->enum('status', ['registered', 'canceled', 'attended'])->default('registered');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
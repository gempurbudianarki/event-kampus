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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Event
            $table->text('description'); // Deskripsi lengkap
            $table->string('banner')->nullable(); // Foto banner
            $table->dateTime('event_date'); // Tanggal & Jam pelaksanaan
            $table->string('location'); // Lokasi (Aula, Zoom, dll)
            $table->integer('quota'); // Jumlah kursi tersedia
            // Status: draft (diedit), published (tayang), closed (selesai/penuh)
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
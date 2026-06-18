<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembuangan_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->integer('qty');
            $table->date('tanggal_buang');
            $table->string('keterangan')->nullable(); // e.g. "Otomatis - Kadaluarsa 18/06/2026"
            $table->enum('metode', ['manual', 'otomatis'])->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembuangan_stoks');
    }
};

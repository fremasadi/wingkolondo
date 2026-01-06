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
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();

            // Relasi ke pesanan
            $table->foreignId('pesanan_id')
                ->constrained('pesanans')
                ->cascadeOnDelete();

            // Kurir (user)
            $table->foreignId('kurir_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Data distribusi
            $table->date('tanggal_kirim');
            $table->enum('status_pengiriman', [
                'pending',
                'dikirim',
                'selesai',
                'retur'
            ])->default('pending');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};

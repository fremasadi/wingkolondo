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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            // Relasi ke toko
            $table->foreignId('toko_id')
                ->constrained('tokos')
                ->cascadeOnDelete();

            // Data pesanan
            $table->date('tanggal_pesanan');
            $table->date('tanggal_kirim')->nullable();

            // Status pesanan
            $table->enum('status_pesanan', [
                'draft',
                'dikonfirmasi',
                'diproses',
                'dikirim',
                'selesai',
                'batal'
            ])->default('draft');

            // Total harga pesanan
            $table->decimal('total_harga', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};

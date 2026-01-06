<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('piutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();

            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();

            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('sisa_tagihan', 12, 2);

            $table->date('jatuh_tempo')->nullable();

            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutangs');
    }
};

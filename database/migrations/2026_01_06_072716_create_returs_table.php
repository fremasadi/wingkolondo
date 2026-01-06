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
        Schema::create('returs', function (Blueprint $table) {
            $table->id();
            // Relasi utama
            $table->foreignId('distribusi_id')->constrained('distribusis')->cascadeOnDelete();

            // Tanggal & status
            $table->date('tanggal_retur');

            // Alasan
            $table->text('alasan')->nullable();

            // Nilai retur (uang)
            $table->decimal('total_retur', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('order_code', 40)->nullable()->unique()->after('id');
        });

        DB::table('pesanans')
            ->select(['id', 'tanggal_pesanan'])
            ->chunkById(100, function ($pesanans) {
                foreach ($pesanans as $pesanan) {
                    $date = $pesanan->tanggal_pesanan
                        ? date('Ymd', strtotime($pesanan->tanggal_pesanan))
                        : date('Ymd');

                    DB::table('pesanans')
                        ->where('id', $pesanan->id)
                        ->update([
                            'order_code' => 'WL-' . $date . '-' . str_pad((string) $pesanan->id, 5, '0', STR_PAD_LEFT),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropUnique(['order_code']);
            $table->dropColumn('order_code');
        });
    }
};

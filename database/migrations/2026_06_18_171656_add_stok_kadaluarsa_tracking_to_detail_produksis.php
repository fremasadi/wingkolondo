<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_produksis', function (Blueprint $table) {
            // Track how many from this batch have been disposed (buang)
            $table->integer('qty_dibuang')->default(0)->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('detail_produksis', function (Blueprint $table) {
            $table->dropColumn('qty_dibuang');
        });
    }
};

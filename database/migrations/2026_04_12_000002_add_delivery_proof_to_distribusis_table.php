<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('catatan');
            $table->decimal('delivery_latitude', 10, 8)->nullable()->after('delivered_at');
            $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
            $table->string('delivery_photo')->nullable()->after('delivery_longitude');
            $table->text('delivery_note')->nullable()->after('delivery_photo');
            $table->foreignId('approved_by')->nullable()->after('delivery_note')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        DB::statement("
            ALTER TABLE distribusis
            MODIFY status_pengiriman ENUM('pending', 'dikirim', 'terkirim', 'selesai', 'retur')
            NOT NULL DEFAULT 'pending'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE distribusis
            MODIFY status_pengiriman ENUM('pending', 'dikirim', 'selesai', 'retur')
            NOT NULL DEFAULT 'pending'
        ");

        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'delivered_at',
                'delivery_latitude',
                'delivery_longitude',
                'delivery_photo',
                'delivery_note',
                'approved_at',
            ]);
        });
    }
};

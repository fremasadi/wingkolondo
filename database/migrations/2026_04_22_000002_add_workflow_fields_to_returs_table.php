<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returs', function (Blueprint $table) {
            $table->foreignId('kurir_id')->nullable()->after('distribusi_id')->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('ditugaskan')->after('tanggal_retur');
            $table->string('refund_method', 30)->default('uang_tunai')->after('alasan');
            $table->decimal('total_refund', 15, 2)->default(0)->after('total_retur');
            $table->date('tanggal_pengambilan')->nullable()->after('total_refund');
            $table->timestamp('picked_up_at')->nullable()->after('tanggal_pengambilan');
            $table->decimal('pickup_latitude', 10, 8)->nullable()->after('picked_up_at');
            $table->decimal('pickup_longitude', 11, 8)->nullable()->after('pickup_latitude');
            $table->string('pickup_photo')->nullable()->after('pickup_longitude');
            $table->text('pickup_note')->nullable()->after('pickup_photo');
            $table->foreignId('approved_by')->nullable()->after('pickup_note')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        Schema::table('detail_returs', function (Blueprint $table) {
            $table->string('kondisi', 30)->default('rusak')->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('detail_returs', function (Blueprint $table) {
            $table->dropColumn('kondisi');
        });

        Schema::table('returs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('kurir_id');
            $table->dropColumn([
                'status',
                'refund_method',
                'total_refund',
                'tanggal_pengambilan',
                'picked_up_at',
                'pickup_latitude',
                'pickup_longitude',
                'pickup_photo',
                'pickup_note',
                'approved_at',
            ]);
        });
    }
};

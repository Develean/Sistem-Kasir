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
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'diskon')) {
                $table->integer('diskon')->default(0)->after('total_harga');
            }
            if (!Schema::hasColumn('transaksis', 'nomor_referensi')) {
                $table->string('nomor_referensi')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('transaksis', 'bank')) {
                $table->string('bank')->nullable()->after('nomor_referensi');
            }
            if (!Schema::hasColumn('transaksis', 'nama_pelanggan')) {
                $table->string('nama_pelanggan')->default('Pelanggan Umum')->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'diskon')) {
                $table->dropColumn('diskon');
            }
            if (Schema::hasColumn('transaksis', 'nomor_referensi')) {
                $table->dropColumn('nomor_referensi');
            }
            if (Schema::hasColumn('transaksis', 'bank')) {
                $table->dropColumn('bank');
            }
            if (Schema::hasColumn('transaksis', 'nama_pelanggan')) {
                $table->dropColumn('nama_pelanggan');
            }
        });
    }
};

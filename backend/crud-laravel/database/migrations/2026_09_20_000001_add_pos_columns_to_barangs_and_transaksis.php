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
        // Tambahkan kolom kategori dan harga_modal ke tabel barangs
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'kategori')) {
                $table->string('kategori')->nullable()->after('nama_barang');
            }
            if (!Schema::hasColumn('barangs', 'harga_modal')) {
                $table->integer('harga_modal')->default(0)->after('harga');
            }
        });

        // Tambahkan kolom user_id, metode_pembayaran, dan status ke tabel transaksis
        Schema::table('transaksis', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksis', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('transaksis', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->default('tunai')->after('kembali');
            }
            if (!Schema::hasColumn('transaksis', 'status')) {
                $table->string('status')->default('selesai')->after('metode_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (Schema::hasColumn('barangs', 'kategori')) {
                $table->dropColumn('kategori');
            }
            if (Schema::hasColumn('barangs', 'harga_modal')) {
                $table->dropColumn('harga_modal');
            }
        });

        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('transaksis', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('transaksis', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

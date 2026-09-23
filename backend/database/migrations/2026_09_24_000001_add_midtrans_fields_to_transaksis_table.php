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
            if (!Schema::hasColumn('transaksis', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('nomor_referensi');
            }
            if (!Schema::hasColumn('transaksis', 'snap_redirect_url')) {
                $table->text('snap_redirect_url')->nullable()->after('snap_token');
            }
            if (!Schema::hasColumn('transaksis', 'payment_gateway_response')) {
                $table->json('payment_gateway_response')->nullable()->after('snap_redirect_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'payment_gateway_response')) {
                $table->dropColumn('payment_gateway_response');
            }
            if (Schema::hasColumn('transaksis', 'snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }
            if (Schema::hasColumn('transaksis', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
        });
    }
};

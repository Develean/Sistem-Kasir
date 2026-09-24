<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toko_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko')->default('TOKO SEJAHTRA');
            $table->string('alamat')->default('Jl. Sejahtera No. 1');
            $table->string('telepon')->default('0812-3456-7890');
            $table->longText('logo')->nullable(); // Base64 data URL atau URL gambar
            $table->string('footer_struk')->default('Terima kasih atas kunjungan Anda!');
            $table->timestamps();
        });

        // Insert default row
        DB::table('toko_settings')->insert([
            'nama_toko'    => 'TOKO SEJAHTRA',
            'alamat'       => 'Jl. Sejahtera No. 1',
            'telepon'      => '0812-3456-7890',
            'logo'         => null,
            'footer_struk' => 'Terima kasih atas kunjungan Anda!',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('toko_settings');
    }
};

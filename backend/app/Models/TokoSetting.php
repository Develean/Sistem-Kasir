<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoSetting extends Model
{
    protected $table = 'toko_settings';

    protected $fillable = [
        'nama_toko',
        'alamat',
        'telepon',
        'logo',
        'footer_struk',
    ];

    /**
     * Dapatkan instance pengaturan toko tunggal (singleton record)
     */
    public static function get(): self
    {
        return static::firstOrCreate([], [
            'nama_toko'    => 'TOKO SEJAHTRA',
            'alamat'       => 'Jl. Sejahtera No. 1',
            'telepon'      => '0812-3456-7890',
            'logo'         => null,
            'footer_struk' => 'Terima kasih atas kunjungan Anda!',
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'user_id',
        'nama_pelanggan',
        'no_nota',
        'total_harga',
        'diskon',
        'bayar',
        'kembali',
        'metode_pembayaran',
        'bank',
        'nomor_referensi',
        'status',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

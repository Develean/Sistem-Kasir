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
        'snap_token',
        'snap_redirect_url',
        'payment_gateway_response',
    ];

    protected $casts = [
        'items' => 'array',
        'payment_gateway_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

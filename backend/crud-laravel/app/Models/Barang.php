<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // Nama tabel di database (sesuaikan jika di phpMyAdmin namanya 'barangs' atau 'barang')
    protected $table = 'barangs'; 

    // Kolom yang diizinkan untuk diisi data
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'harga',
        'harga_modal',
        'stok',
    ];
}

<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kasir = User::where('role', 'kasir')->first();

        $adminName = $admin ? $admin->name : 'Admin Toko';
        $kasirName = $kasir ? $kasir->name : 'Staff Kasir';

        $adminId = $admin ? $admin->id : 1;
        $kasirId = $kasir ? $kasir->id : 2;

        $now = now();

        $samples = [
            [
                'user_id'     => $adminId,
                'user_name'   => $adminName,
                'user_role'   => 'admin',
                'action'      => 'login',
                'description' => "Pengguna {$adminName} (admin) berhasil masuk ke sistem",
                'created_at'  => $now->copy()->subMinutes(120),
            ],
            [
                'user_id'     => $kasirId,
                'user_name'   => $kasirName,
                'user_role'   => 'kasir',
                'action'      => 'login',
                'description' => "Pengguna {$kasirName} (kasir) berhasil masuk ke sistem",
                'created_at'  => $now->copy()->subMinutes(100),
            ],
            [
                'user_id'     => $kasirId,
                'user_name'   => $kasirName,
                'user_role'   => 'kasir',
                'action'      => 'transaksi',
                'description' => "Membuat transaksi POS #TRX-POS-2609-001 senilai Rp 85.000 (TUNAI)",
                'created_at'  => $now->copy()->subMinutes(75),
            ],
            [
                'user_id'     => $kasirId,
                'user_name'   => $kasirName,
                'user_role'   => 'kasir',
                'action'      => 'transaksi',
                'description' => "Membuat transaksi POS #TRX-POS-2609-002 senilai Rp 140.000 (MIDTRANS / QRIS)",
                'created_at'  => $now->copy()->subMinutes(50),
            ],
            [
                'user_id'     => $kasirId,
                'user_name'   => $kasirName,
                'user_role'   => 'kasir',
                'action'      => 'barang',
                'description' => "Menambahkan stok baru untuk barang 'Kopi Susu Gula Aren' (+20 unit)",
                'created_at'  => $now->copy()->subMinutes(35),
            ],
            [
                'user_id'     => $adminId,
                'user_name'   => $adminName,
                'user_role'   => 'admin',
                'action'      => 'user_management',
                'description' => "Membuat akun kasir baru 'Staff Kasir 2' (kasir2@gmail.com)",
                'created_at'  => $now->copy()->subMinutes(20),
            ],
            [
                'user_id'     => $adminId,
                'user_name'   => $adminName,
                'user_role'   => 'admin',
                'action'      => 'batal_transaksi',
                'description' => "Membatalkan (void) transaksi #TRX-POS-2609-001 dan mengembalikan stok produk",
                'created_at'  => $now->copy()->subMinutes(10),
            ],
        ];

        foreach ($samples as $sample) {
            ActivityLog::create([
                'user_id'     => $sample['user_id'],
                'user_name'   => $sample['user_name'],
                'user_role'   => $sample['user_role'],
                'action'      => $sample['action'],
                'description' => $sample['description'],
                'ip_address'  => '127.0.0.1',
                'created_at'  => $sample['created_at'],
                'updated_at'  => $sample['created_at'],
            ]);
        }
    }
}

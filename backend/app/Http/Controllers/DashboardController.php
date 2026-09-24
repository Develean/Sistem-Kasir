<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfPrevMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfPrevMonth = Carbon::now()->subMonth()->endOfMonth();

        // 1. Omzet & Transaksi Hari Ini
        $trxHariIniQuery = Transaksi::where('status', '!=', 'dibatalkan')
            ->whereDate('created_at', $today);
        $omzetHariIni = (int) $trxHariIniQuery->sum('total_harga');
        $transaksiHariIni = (int) $trxHariIniQuery->count();

        // Kemarin untuk perbandingan
        $yesterday = Carbon::yesterday();
        $omzetKemarin = (int) Transaksi::where('status', '!=', 'dibatalkan')
            ->whereDate('created_at', $yesterday)
            ->sum('total_harga');

        // 2. Omzet & Transaksi Bulan Ini
        $trxBulanIniQuery = Transaksi::where('status', '!=', 'dibatalkan')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        $omzetBulanIni = (int) $trxBulanIniQuery->sum('total_harga');
        $transaksiBulanIni = (int) $trxBulanIniQuery->count();

        // Bulan lalu untuk perbandingan
        $omzetBulanLalu = (int) Transaksi::where('status', '!=', 'dibatalkan')
            ->whereBetween('created_at', [$startOfPrevMonth, $endOfPrevMonth])
            ->sum('total_harga');

        // Total Transaksi & Omzet Keseluruhan
        $totalTransaksi = (int) Transaksi::where('status', '!=', 'dibatalkan')->count();
        $totalOmzet = (int) Transaksi::where('status', '!=', 'dibatalkan')->sum('total_harga');

        // 3. Stok Kritis (Stok <= 5)
        $stokKritis = Barang::where('stok', '<=', 5)
            ->orderBy('stok', 'asc')
            ->limit(10)
            ->get(['id', 'kode_barang', 'nama_barang', 'kategori', 'stok', 'harga', 'harga_modal']);
        $totalStokKritis = Barang::where('stok', '<=', 5)->count();
        $totalProduk = Barang::count();

        // 4. Produk Terlaris (dari item transaksi yang valid)
        $allValidTrx = Transaksi::where('status', '!=', 'dibatalkan')
            ->select(['id', 'items', 'created_at'])
            ->get();

        $produkTerlarisMap = [];
        $totalItemTerjual = 0;

        foreach ($allValidTrx as $trx) {
            if (!empty($trx->items) && is_array($trx->items)) {
                foreach ($trx->items as $item) {
                    $key = $item['nama_barang'] ?? ('ID_' . ($item['id'] ?? 'unknown'));
                    $qty = (int) ($item['qty'] ?? 1);
                    $harga = (int) ($item['harga'] ?? 0);
                    $totalItemTerjual += $qty;

                    if (!isset($produkTerlarisMap[$key])) {
                        $produkTerlarisMap[$key] = [
                            'id'            => $item['id'] ?? null,
                            'kode_barang'   => $item['kode_barang'] ?? '-',
                            'nama_barang'   => $item['nama_barang'] ?? 'Produk Tanpa Nama',
                            'harga'         => $harga,
                            'total_terjual' => 0,
                            'total_omzet'   => 0,
                        ];
                    }

                    $produkTerlarisMap[$key]['total_terjual'] += $qty;
                    $produkTerlarisMap[$key]['total_omzet'] += ($qty * $harga);
                }
            }
        }

        // Urutkan produk terlaris berdasarkan qty terjual terbanyak
        $produkTerlarisArray = array_values($produkTerlarisMap);
        usort($produkTerlarisArray, function ($a, $b) {
            return $b['total_terjual'] <=> $a['total_terjual'];
        });

        // Ambil top 5 dan hitung persentase
        $topProduk = array_slice($produkTerlarisArray, 0, 5);
        $topProduk = array_map(function ($prod) use ($totalItemTerjual) {
            $prod['persentase'] = $totalItemTerjual > 0 
                ? round(($prod['total_terjual'] / $totalItemTerjual) * 100, 1) 
                : 0;
            return $prod;
        }, $topProduk);

        // 5. Grafik 7 Hari Terakhir (Daily Trend)
        $grafik7Hari = [];
        $namaHariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->toDateString();
            
            $trxDay = Transaksi::where('status', '!=', 'dibatalkan')
                ->whereDate('created_at', $tglStr);

            $dayNameEn = $tgl->format('l');
            $dayNameIndo = $namaHariIndo[$dayNameEn] ?? $tgl->format('D');

            $grafik7Hari[] = [
                'date'       => $tglStr,
                'label'      => $tgl->format('d M'),
                'hari'       => $dayNameIndo,
                'omzet'      => (int) $trxDay->sum('total_harga'),
                'transaksi'  => (int) $trxDay->count(),
            ];
        }

        // 6. Grafik 30 Hari Terakhir (opsional jika admin ingin rentang lebih luas)
        $grafik30Hari = [];
        for ($i = 29; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->toDateString();

            $trxDay = Transaksi::where('status', '!=', 'dibatalkan')
                ->whereDate('created_at', $tglStr);

            $grafik30Hari[] = [
                'date'       => $tglStr,
                'label'      => $tgl->format('d/m'),
                'omzet'      => (int) $trxDay->sum('total_harga'),
                'transaksi'  => (int) $trxDay->count(),
            ];
        }

        // 7. Breakdown Metode Pembayaran Bulan Ini
        $metodePembayaran = Transaksi::where('status', '!=', 'dibatalkan')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select('metode_pembayaran', DB::raw('count(*) as count'), DB::raw('sum(total_harga) as total'))
            ->groupBy('metode_pembayaran')
            ->get();

        return response()->json([
            'omzet' => [
                'hari_ini'       => $omzetHariIni,
                'kemarin'        => $omzetKemarin,
                'bulan_ini'      => $omzetBulanIni,
                'bulan_lalu'     => $omzetBulanLalu,
                'total_omzet'    => $totalOmzet,
            ],
            'transaksi' => [
                'hari_ini'       => $transaksiHariIni,
                'bulan_ini'      => $transaksiBulanIni,
                'total'          => $totalTransaksi,
            ],
            'stok' => [
                'kritis_list'    => $stokKritis,
                'total_kritis'   => $totalStokKritis,
                'total_produk'   => $totalProduk,
            ],
            'produk_terlaris'    => $topProduk,
            'total_item_terjual' => $totalItemTerjual,
            'grafik' => [
                'tujuh_hari'     => $grafik7Hari,
                'tiga_puluh_hari'=> $grafik30Hari,
            ],
            'metode_pembayaran'  => $metodePembayaran,
        ], 200);
    }
}

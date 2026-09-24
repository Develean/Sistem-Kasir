<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    // Mengambil daftar riwayat transaksi
    public function index(Request $request)
    {
        $query = Transaksi::with('user')->orderByDesc('created_at');

        // Filter tanggal (format Y-m-d)
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter metode pembayaran
        if ($request->filled('metode') && $request->metode !== 'all') {
            $query->where('metode_pembayaran', $request->metode);
        }

        $transaksis = $query->get()->map(function (Transaksi $trx) {
            // Hitung total modal (HPP) dari seluruh item transaksi
            $totalModal = 0;
            if (!empty($trx->items) && is_array($trx->items)) {
                foreach ($trx->items as $item) {
                    $totalModal += ((int)($item['harga_modal'] ?? 0)) * ((int)($item['qty'] ?? 1));
                }
            }

            return [
                'id'                => $trx->id,
                'noStruk'           => $trx->no_nota,
                'tanggal'           => $trx->created_at->format('d-m-Y H:i:s'),
                'kasir'             => $trx->user ? $trx->user->name : 'Kasir',
                'nama_pelanggan'    => $trx->nama_pelanggan ?? 'Pelanggan Umum',
                'metode_pembayaran' => $trx->metode_pembayaran ?? 'tunai',
                'bank'              => $trx->bank,
                'nomor_referensi'   => $trx->nomor_referensi,
                'status'            => $trx->status ?? 'selesai',
                'items'             => $trx->items ?? [],
                'subtotal'          => $trx->total_harga + ($trx->diskon ?? 0),
                'diskon'            => $trx->diskon ?? 0,
                'total'             => $trx->total_harga,
                'total_modal'       => $totalModal,
                'laba_kotor'        => max(0, $trx->total_harga - $totalModal),
                'bayar'             => $trx->bayar,
                'kembalian'         => $trx->kembali,
                'snap_token'        => $trx->snap_token,
                'snap_redirect_url' => $trx->snap_redirect_url,
            ];
        });

        return response()->json($transaksis, 200);
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'bayar'             => 'required|numeric|min:0',
            'metode_pembayaran' => 'nullable|string|max:50',
            'bank'              => 'nullable|string|max:50',
            'nomor_referensi'   => 'nullable|string|max:100',
            'nama_pelanggan'    => 'nullable|string|max:100',
            'diskon'            => 'nullable|integer|min:0',
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required|exists:barangs,id',
            'items.*.qty'       => 'required|integer|min:1',
        ]);

        $user = $request->user();

        try {
            $transaksi = DB::transaction(function () use ($request, $user) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($request->items as $item) {
                    $barang = Barang::lockForUpdate()->findOrFail($item['id']);

                    if ($barang->stok < $item['qty']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok {$barang->nama_barang} tidak mencukupi (sisa: {$barang->stok})"
                        ]);
                    }

                    $subtotal += $barang->harga * $item['qty'];
                    $barang->decrement('stok', $item['qty']);

                    $itemsData[] = [
                        'id'          => $barang->id,
                        'kode_barang' => $barang->kode_barang,
                        'nama_barang' => $barang->nama_barang,
                        'harga'       => $barang->harga,
                        'harga_modal' => $barang->harga_modal ?? 0,
                        'qty'         => (int)$item['qty'],
                    ];
                }

                $diskon = min($subtotal, max(0, (int)($request->diskon ?? 0)));
                $totalHarga = max(0, $subtotal - $diskon);

                $metode = strtolower($request->metode_pembayaran ?? 'tunai');
                $bayar = (float)$request->bayar;

                // Jika metode non-tunai (QRIS / Transfer), pastikan bayar tepat atau minimal sama dengan total
                if ($metode !== 'tunai' && $bayar < $totalHarga) {
                    $bayar = $totalHarga;
                }

                if ($bayar < $totalHarga) {
                    throw ValidationException::withMessages([
                        'bayar' => 'Uang pembayaran masih kurang!'
                    ]);
                }

                $kembali = max(0, $bayar - $totalHarga);
                $noNota = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                // Buat nomor referensi jika belum dikirim (khusus QRIS)
                $noRef = $request->nomor_referensi;
                if ($metode === 'qris' && empty($noRef)) {
                    $noRef = 'QRIS-' . date('Ymd') . '-' . rand(10000, 99999);
                }

                return Transaksi::create([
                    'user_id'           => $user ? $user->id : null,
                    'nama_pelanggan'    => $request->nama_pelanggan ?: 'Pelanggan Umum',
                    'no_nota'           => $noNota,
                    'total_harga'       => $totalHarga,
                    'diskon'            => $diskon,
                    'bayar'             => $bayar,
                    'kembali'           => $kembali,
                    'metode_pembayaran' => $metode,
                    'bank'              => $request->bank,
                    'nomor_referensi'   => $noRef,
                    'status'            => 'selesai',
                    'items'             => $itemsData,
                ]);
            });

            if ($transaksi) {
                \App\Models\ActivityLog::record(
                    $request->user() ?: ($transaksi->kasir ?? 'Kasir'),
                    'transaksi',
                    "Membuat transaksi POS #{$transaksi->no_nota} senilai Rp " . number_format($transaksi->total, 0, ',', '.') . " (" . strtoupper($transaksi->metode_pembayaran) . ")",
                    $request
                );
            }

            return response()->json([
                'message' => 'Transaksi berhasil diproses',
                'data'    => $transaksi
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    // Membatalkan transaksi (Void / Batal Pending) dan mengembalikan stok barang
    public function batal(Request $request, $id)
    {
        $user = $request->user();
        $trx = Transaksi::findOrFail($id);

        // Jika transaksi sudah selesai, hanya administrator yang berhak void
        if ($trx->status === 'selesai' && (!$user || !$user->isAdmin())) {
            return response()->json([
                'message' => 'Akses ditolak: Hanya Administrator yang berhak membatalkan (void) transaksi yang sudah selesai.'
            ], 403);
        }

        try {
            $transaksi = DB::transaction(function () use ($trx) {
                if ($trx->status === 'dibatalkan') {
                    throw ValidationException::withMessages([
                        'transaksi' => 'Transaksi ini sudah pernah dibatalkan sebelumnya.'
                    ]);
                }

                if (!empty($trx->items) && is_array($trx->items)) {
                    foreach ($trx->items as $item) {
                        if (isset($item['id']) && isset($item['qty'])) {
                            Barang::where('id', $item['id'])->increment('stok', (int)$item['qty']);
                        }
                    }
                }

                $trx->update([
                    'status' => 'dibatalkan'
                ]);

                return $trx;
            });

            // Jika transaksi menggunakan gateway Midtrans, coba batalkan di Midtrans
            if ($transaksi->metode_pembayaran === 'midtrans') {
                try {
                    app(\App\Services\MidtransService::class)->cancelTransaction($transaksi->no_nota);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Gagal cancel transaksi Midtrans di gateway: " . $e->getMessage());
                }
            }

            \App\Models\ActivityLog::record(
                request()->user() ?: 'Kasir',
                'batal_transaksi',
                "Membatalkan (" . ($transaksi->status === 'selesai' ? 'void' : 'batal pending') . ") transaksi #{$transaksi->no_nota} dan mengembalikan stok produk",
                request()
            );

            return response()->json([
                'message' => 'Transaksi berhasil dibatalkan dan stok produk telah dikembalikan.',
                'data'    => $transaksi
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        return $this->batal($id);
    }
}
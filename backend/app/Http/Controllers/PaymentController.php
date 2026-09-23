<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Membuat Transaksi Kasir dan Token Snap Midtrans
     */
    public function createSnap(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'nullable|string|max:100',
            'diskon'         => 'nullable|integer|min:0',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:barangs,id',
            'items.*.qty'    => 'required|integer|min:1',
        ]);

        $user = $request->user();

        try {
            $result = DB::transaction(function () use ($request, $user) {
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
                        'qty'         => (int) $item['qty'],
                    ];
                }

                $diskon = min($subtotal, max(0, (int) ($request->diskon ?? 0)));
                $totalHarga = max(0, $subtotal - $diskon);

                if ($totalHarga <= 0) {
                    throw ValidationException::withMessages([
                        'total' => 'Total tagihan pembayaran online minimal Rp 1'
                    ]);
                }

                $noNota = 'TRX-MID-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                $transaksi = Transaksi::create([
                    'user_id'           => $user ? $user->id : null,
                    'nama_pelanggan'    => $request->nama_pelanggan ?: 'Pelanggan Umum',
                    'no_nota'           => $noNota,
                    'total_harga'       => $totalHarga,
                    'diskon'            => $diskon,
                    'bayar'             => 0,
                    'kembali'           => 0,
                    'metode_pembayaran' => 'midtrans',
                    'bank'              => 'Midtrans Gateway',
                    'nomor_referensi'   => null,
                    'status'            => 'pending',
                    'items'             => $itemsData,
                ]);

                // Buat token Snap via MidtransService
                $snapData = $this->midtransService->createSnapTransaction($transaksi, $itemsData, $request->nama_pelanggan);

                $transaksi->update([
                    'snap_token'        => $snapData['token'],
                    'snap_redirect_url' => $snapData['redirect_url'],
                ]);

                return [
                    'transaksi'  => $transaksi,
                    'snap_token' => $snapData['token'],
                    'snap_url'   => $snapData['redirect_url'],
                    'is_mock'    => $snapData['is_mock'],
                    'client_key' => $snapData['client_key'],
                ];
            });

            return response()->json([
                'message'           => 'Token transaksi Midtrans berhasil dibuat',
                'no_nota'           => $result['transaksi']->no_nota,
                'total_harga'       => $result['transaksi']->total_harga,
                'snap_token'        => $result['snap_token'],
                'snap_redirect_url' => $result['snap_url'],
                'is_mock'           => $result['is_mock'],
                'client_key'        => $result['client_key'],
                'transaksi'         => $result['transaksi'],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        } catch (\Exception $e) {
            Log::error("Error createSnap: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memproses pembayaran Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook Notifikasi Callback dari Midtrans
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = $request->order_id;
        $statusCode = (string) $request->status_code;
        $grossAmount = (string) $request->gross_amount;
        $signatureKey = (string) $request->signature_key;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        if (empty($orderId)) {
            return response()->json(['message' => 'Order ID tidak ditemukan'], 400);
        }

        // Verifikasi signature jika bukan mode simulasi mock
        if (!$this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning("Midtrans Webhook Signature Mismatch for {$orderId}");
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transaksi = Transaksi::where('no_nota', $orderId)->first();
        if (!$transaksi) {
            Log::warning("Transaksi {$orderId} tidak ditemukan saat webhook diproses");
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Simpan log payload gateway
        $transaksi->payment_gateway_response = $payload;

        // Tangani perubahan status pembayaran
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $this->tandaiSelesai($transaksi, $payload);
            }
        } elseif ($transactionStatus === 'settlement') {
            $this->tandaiSelesai($transaksi, $payload);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $this->batalkanOtomatis($transaksi, $transactionStatus);
        } elseif ($transactionStatus === 'pending') {
            $transaksi->status = 'pending';
            $transaksi->save();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Webhook Midtrans berhasil diproses'
        ], 200);
    }

    /**
     * Polling Cek Status Transaksi oleh Kasir Frontend
     */
    public function checkStatus(string $noNota)
    {
        $transaksi = Transaksi::where('no_nota', $noNota)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Jika masih pending dan Midtrans sudah terkonfigurasi, periksa langsung ke server Midtrans
        if ($transaksi->status === 'pending' && $this->midtransService->isConfigured()) {
            $midtransStatus = $this->midtransService->getTransactionStatus($noNota);
            if ($midtransStatus) {
                $statusTrx = $midtransStatus['transaction_status'] ?? '';
                if ($statusTrx === 'settlement' || ($statusTrx === 'capture' && ($midtransStatus['fraud_status'] ?? '') === 'accept')) {
                    $this->tandaiSelesai($transaksi, $midtransStatus);
                } elseif (in_array($statusTrx, ['cancel', 'deny', 'expire'])) {
                    $this->batalkanOtomatis($transaksi, $statusTrx);
                }
            }
        }

        return response()->json([
            'no_nota'   => $transaksi->no_nota,
            'status'    => $transaksi->status,
            'total'     => $transaksi->total_harga,
            'bayar'     => $transaksi->bayar,
            'bank'      => $transaksi->bank,
            'transaksi' => $transaksi,
        ], 200);
    }

    /**
     * Endpoint Simulasi Bayar Sukses untuk Pengujian Kasir Lokal (Tanpa Ngrok)
     */
    public function simulasiSukses(string $noNota)
    {
        $transaksi = Transaksi::where('no_nota', $noNota)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($transaksi->status === 'selesai') {
            return response()->json([
                'message' => 'Transaksi sudah berstatus selesai sebelumnya',
                'data'    => $transaksi
            ], 200);
        }

        $mockPayload = [
            'transaction_status' => 'settlement',
            'payment_type'       => 'qris',
            'issuer'             => 'gopay',
            'transaction_id'     => 'SIM-MID-' . date('YmdHis'),
            'settlement_time'    => date('Y-m-d H:i:s'),
        ];

        $this->tandaiSelesai($transaksi, $mockPayload);

        return response()->json([
            'message' => 'Simulasi pembayaran Midtrans berhasil diselesaikan!',
            'data'    => $transaksi
        ], 200);
    }

    /**
     * Membatalkan transaksi pending kasir dan mengembalikan stok produk
     */
    public function batal(string $noNota)
    {
        $transaksi = Transaksi::where('no_nota', $noNota)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($transaksi->status === 'selesai') {
            return response()->json(['message' => 'Transaksi yang sudah selesai tidak dapat dibatalkan di sini.'], 400);
        }

        if ($transaksi->status === 'dibatalkan') {
            return response()->json(['message' => 'Transaksi sudah dibatalkan sebelumnya.'], 200);
        }

        $this->batalkanOtomatis($transaksi, 'kasir_cancel');
        $this->midtransService->cancelTransaction($noNota);

        return response()->json([
            'message' => 'Transaksi Midtrans berhasil dibatalkan dan stok telah dikembalikan.',
            'data'    => $transaksi
        ], 200);
    }

    /**
     * Helper: Menandai transaksi selesai / lunas
     */
    protected function tandaiSelesai(Transaksi $transaksi, array $payload): void
    {
        $tipe = $payload['payment_type'] ?? 'midtrans';
        $issuer = $payload['issuer'] ?? ($payload['bank'] ?? '');
        $channel = $issuer ? strtoupper("{$tipe} ({$issuer})") : strtoupper($tipe);

        $transaksi->update([
            'status'                  => 'selesai',
            'bayar'                   => $transaksi->total_harga,
            'kembali'                 => 0,
            'bank'                    => $channel,
            'nomor_referensi'         => $payload['transaction_id'] ?? ($payload['order_id'] ?? 'REF-MIDTRANS'),
            'payment_gateway_response'=> $payload,
        ]);
    }

    /**
     * Helper: Membatalkan transaksi dan mengembalikan stok
     */
    protected function batalkanOtomatis(Transaksi $transaksi, string $alasan): void
    {
        DB::transaction(function () use ($transaksi, $alasan) {
            if (!empty($transaksi->items) && is_array($transaksi->items)) {
                foreach ($transaksi->items as $item) {
                    if (isset($item['id']) && isset($item['qty'])) {
                        Barang::where('id', $item['id'])->increment('stok', (int) $item['qty']);
                    }
                }
            }

            $transaksi->update([
                'status' => 'dibatalkan',
                'nomor_referensi' => "Dibatalkan: {$alasan}",
            ]);
        });
    }
}

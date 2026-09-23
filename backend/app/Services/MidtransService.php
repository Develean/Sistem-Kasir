<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapUrl;
    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', '');
        $this->clientKey = config('midtrans.client_key', '');
        $this->isProduction = (bool) config('midtrans.is_production', false);
        $this->snapUrl = config('midtrans.snap_url');
        $this->apiUrl = config('midtrans.api_url');
    }

    /**
     * Memeriksa apakah kredensial Midtrans siap digunakan atau masih placeholder
     */
    public function isConfigured(): bool
    {
        return !empty($this->serverKey) && !str_starts_with($this->serverKey, 'SB-Mid-server-YOUR_');
    }

    /**
     * Membuat Snap Transaction Token untuk Transaksi Kasir
     *
     * @param Transaksi $transaksi
     * @param array $items
     * @param string|null $customerName
     * @return array ['token' => string, 'redirect_url' => string, 'is_mock' => bool]
     */
    public function createSnapTransaction(Transaksi $transaksi, array $items = [], ?string $customerName = 'Pelanggan Umum'): array
    {
        // Jika Server Key belum diset, aktifkan mode simulasi otomatis untuk pengujian lokal
        if (!$this->isConfigured()) {
            $mockToken = 'MOCK-SNAP-' . strtoupper(bin2hex(random_bytes(10)));
            $mockUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$mockToken}";

            Log::info("Midtrans mock mode digunakan untuk No Nota: {$transaksi->no_nota}");

            return [
                'token'        => $mockToken,
                'redirect_url' => $mockUrl,
                'is_mock'      => true,
                'client_key'   => $this->clientKey ?: 'SB-Mid-client-mock',
            ];
        }

        // Format Item Details sesuai aturan Midtrans
        $itemDetails = [];
        $totalItems = 0;

        foreach ($items as $item) {
            $harga = (int) ($item['harga'] ?? 0);
            $qty = (int) ($item['qty'] ?? 1);
            $nama = substr($item['nama_barang'] ?? ('Item #' . $item['id']), 0, 50);

            $itemDetails[] = [
                'id'       => (string) ($item['id'] ?? uniqid()),
                'price'    => $harga,
                'quantity' => $qty,
                'name'     => $nama,
            ];
            $totalItems += ($harga * $qty);
        }

        // Jika ada diskon, tambahkan item penyesuaian diskon bernilai negatif
        $diskon = (int) ($transaksi->diskon ?? 0);
        if ($diskon > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -$diskon,
                'quantity' => 1,
                'name'     => 'Potongan Diskon Kasir',
            ];
        }

        $grossAmount = (int) $transaksi->total_harga;

        $payload = [
            'transaction_details' => [
                'order_id'     => $transaksi->no_nota,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $customerName ?: 'Pelanggan Umum',
            ],
        ];

        $authHeader = 'Basic ' . base64_encode($this->serverKey . ':');

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => $authHeader,
        ])->post($this->snapUrl, $payload);

        if (!$response->successful()) {
            Log::error("Midtrans Snap Error: " . $response->body());
            throw new \Exception("Gagal menghubungi Midtrans Snap: " . ($response->json('error_messages.0') ?? $response->body()));
        }

        $resData = $response->json();

        return [
            'token'        => $resData['token'] ?? '',
            'redirect_url' => $resData['redirect_url'] ?? '',
            'is_mock'      => false,
            'client_key'   => $this->clientKey,
        ];
    }

    /**
     * Memverifikasi Signature Key dari Webhook Midtrans
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        if (!$this->isConfigured()) {
            return true; // Bypass verifikasi dalam mode simulasi / mock lokal
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Mengecek status transaksi langsung ke Midtrans API
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $authHeader = 'Basic ' . base64_encode($this->serverKey . ':');
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => $authHeader,
        ])->get("{$this->apiUrl}/{$orderId}/status");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Membatalkan transaksi pending di Midtrans
     */
    public function cancelTransaction(string $orderId): bool
    {
        if (!$this->isConfigured()) {
            return true;
        }

        $authHeader = 'Basic ' . base64_encode($this->serverKey . ':');
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => $authHeader,
        ])->post("{$this->apiUrl}/{$orderId}/cancel");

        return $response->successful();
    }
}

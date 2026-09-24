<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Print receipt via ESC/POS connector.
     *
     * Requires mike42/escpos-php installed (composer require mike42/escpos-php)
     * Configure via .env:
     * PRINT_DRIVER=network|windows
     * PRINT_HOST=192.168.1.100
     * PRINT_PORT=9100
     * PRINT_SHARE=\\\\COMPUTER\\PRINTER_SHARE  (for windows connector)
     */
    public function print(Request $request)
    {
        $data = $request->all();

        // If escpos library not installed, return helpful error
        if (!class_exists('\Mike42\Escpos\Printer')) {
            return response()->json(['error' => 'escpos-php not installed. Run: composer require mike42/escpos-php'], 500);
        }

        try {
            $driver = env('PRINT_DRIVER', 'network');

            if ($driver === 'network') {
                $host = env('PRINT_HOST');
                $port = env('PRINT_PORT', 9100);
                $connector = new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector($host, $port);
            } else {
                // windows share connector expects a printer name like "\\\\HOST\\PRINTER"
                $share = env('PRINT_SHARE');
                $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($share);
            }

            $printer = new \Mike42\Escpos\Printer($connector);

            $setting = \App\Models\TokoSetting::get();

            $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer->text(($setting->nama_toko ?: 'TOKO SEJAHTRA') . "\n");
            if (!empty($setting->alamat)) {
                $printer->text("Alamat: " . $setting->alamat . "\n");
            }
            if (!empty($setting->telepon)) {
                $printer->text("Telp: " . $setting->telepon . "\n");
            }
            $printer->text("No: " . ($data['noStruk'] ?? '-') . "\n");
            if (!empty($data['nama_pelanggan'])) {
                $printer->text("Pelanggan: " . $data['nama_pelanggan'] . "\n");
            }
            if (!empty($data['kasir'])) {
                $printer->text("Kasir: " . $data['kasir'] . "\n");
            }
            $printer->text("-------------------------------\n");

            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $name = substr($item['nama_barang'] ?? '', 0, 18);
                    $qty = $item['qty'] ?? 1;
                    $price = number_format($item['harga'] ?? 0);
                    $printer->text(sprintf("%s x%d %s\n", $name, $qty, $price));
                }
            }

            $printer->text("-------------------------------\n");
            if (!empty($data['diskon']) && $data['diskon'] > 0) {
                $printer->text("Subtotal: " . number_format(($data['total'] ?? 0) + $data['diskon']) . "\n");
                $printer->text("Diskon: -" . number_format($data['diskon']) . "\n");
            }
            $printer->text("Total: " . number_format($data['total'] ?? 0) . "\n");
            $printer->text("Bayar: " . number_format($data['bayar'] ?? 0) . "\n");
            $printer->text("Kembali: " . number_format($data['kembalian'] ?? 0) . "\n");
            $printer->text("Metode: " . strtoupper($data['metode'] ?? 'TUNAI') . "\n");
            if (!empty($data['bank'])) {
                $printer->text("Bank: " . strtoupper($data['bank']) . "\n");
            }
            if (!empty($data['nomor_referensi'])) {
                $printer->text("Ref: " . $data['nomor_referensi'] . "\n");
            }

            $printer->cut();
            $printer->close();

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

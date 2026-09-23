<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PaymentController;

// Public route (login)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// Public Webhook Callback Midtrans (tanpa auth sanctum / CSRF)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Protected routes (semua akses kasir & barang wajib menyertakan token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Info akun & logout
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Manajemen Barang
    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::post('/barang/{id}/tambah-stok', [BarangController::class, 'tambahStok']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

    // Manajemen Transaksi Kasir
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::post('/transaksi/{id}/batal', [TransaksiController::class, 'batal']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);

    // Payment Gateway Midtrans (Snap, Status Check, Simulasi & Batal)
    Route::post('/payment/snap', [PaymentController::class, 'createSnap']);
    Route::get('/payment/{noNota}/status', [PaymentController::class, 'checkStatus']);
    Route::post('/payment/{noNota}/simulasi-sukses', [PaymentController::class, 'simulasiSukses']);
    Route::post('/payment/{noNota}/batal', [PaymentController::class, 'batal']);

    // Cetak Struk ESC/POS
    Route::post('/print', [PrintController::class, 'print']);

    // CRUD Data Pelengkap
    Route::apiResource('data', DataController::class);
});

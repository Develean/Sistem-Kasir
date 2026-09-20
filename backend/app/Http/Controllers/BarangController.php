<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    // Mengambil semua data barang (dengan opsi pencarian & kategori)
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        $barangs = $query->orderBy('nama_barang', 'asc')->get();

        return response()->json($barangs, 200);
    }

    // Menyimpan data barang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'nullable|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'harga_modal' => 'nullable|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ]);

        $barang = Barang::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori'    => $validated['kategori'] ?? null,
            'harga'       => $validated['harga'],
            'harga_modal' => $validated['harga_modal'] ?? 0,
            'stok'        => $validated['stok'],
        ]);

        return response()->json($barang, 201);
    }

    // Mengupdate data barang
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')->ignore($id),
            ],
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'nullable|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'harga_modal' => 'nullable|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ]);

        $barang->update([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori'    => $validated['kategori'] ?? null,
            'harga'       => $validated['harga'],
            'harga_modal' => $validated['harga_modal'] ?? 0,
            'stok'        => $validated['stok'],
        ]);

        return response()->json($barang, 200);
    }

    // Menghapus data barang
    public function destroy($id)
    {
        $barang = Barang::find($id);
        if ($barang) {
            $barang->delete();
            return response()->json(['message' => 'Barang berhasil dihapus'], 200);
        }
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }

    // Tambah stok cepat (Restock masuk)
    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->increment('stok', (int)$request->jumlah);

        return response()->json([
            'message' => "Stok {$barang->nama_barang} berhasil ditambahkan sebanyak {$request->jumlah} unit.",
            'data'    => $barang
        ], 200);
    }
}

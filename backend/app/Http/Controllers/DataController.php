<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DataController extends Controller
{
    // 1. READ (Menampilkan Semua Data)
    public function index()
    {
        $data = Data::all();
        return response()->json($data, 200);
    }

    // 2. CREATE (Menambah Data Baru)
    public function store(Request $request)
    {
        $request->validate([
            'nik'     => 'required',
            'nama'    => 'required',
            'telepon' => 'required',
            'alamat'  => 'required',
        ]);

        $data = Data::create([
            'nik'     => $request->nik,
            'nama'    => $request->nama,
            'telepon' => $request->telepon,
            'alamat'  => $request->alamat,
        ]);

        return response()->json([
            'message' => 'Data berhasil ditambahkan!',
            'data'    => $data
        ], 201);
    }

    // 3. READ DETAIL (Menampilkan 1 Data Spesifik)
    public function show($id)
    {
        $data = Data::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json($data, 200);
    }

    // 4. UPDATE (Mengubah Data)
    public function update(Request $request, $id)
    {
        $data = Data::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan!'], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diperbarui!',
            'data'    => $data
        ], 200);
    }

    // 5. DELETE (Menghapus Data)
    public function destroy($id)
    {
        $data = Data::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan!'], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}

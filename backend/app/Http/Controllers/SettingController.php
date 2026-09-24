<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TokoSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Mengambil data profil dan pengaturan toko
     */
    public function index()
    {
        $setting = TokoSetting::get();
        return response()->json($setting, 200);
    }

    /**
     * Memperbarui profil dan pengaturan toko (Khusus Admin)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_toko'    => 'required|string|max:100',
            'alamat'       => 'nullable|string|max:255',
            'telepon'      => 'nullable|string|max:50',
            'logo'         => 'nullable|string',
            'footer_struk' => 'nullable|string|max:255',
        ]);

        $setting = TokoSetting::get();
        $setting->update([
            'nama_toko'    => $validated['nama_toko'],
            'alamat'       => $validated['alamat'] ?? '',
            'telepon'      => $validated['telepon'] ?? '',
            'logo'         => array_key_exists('logo', $validated) ? $validated['logo'] : $setting->logo,
            'footer_struk' => $validated['footer_struk'] ?? 'Terima kasih atas kunjungan Anda!',
        ]);

        ActivityLog::record(
            $request->user() ?: 'Admin',
            'setting',
            "Memperbarui profil dan pengaturan toko menjadi \"{$setting->nama_toko}\"",
            $request
        );

        return response()->json([
            'message' => 'Pengaturan profil toko berhasil disimpan!',
            'data'    => $setting
        ], 200);
    }
}

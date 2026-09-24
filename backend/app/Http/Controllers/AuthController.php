<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau Password salah!'
            ], 401);
        }

        // Hapus token lama jika ada agar bersih, lalu buat token baru
        $token = $user->createToken('kasir_token')->plainTextToken;

        // Catat aktivitas login
        \App\Models\ActivityLog::record(
            $user,
            'login',
            "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem",
            $request
        );

        return response()->json([
            'message' => 'Login Berhasil!',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role ?? 'kasir',
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            \App\Models\ActivityLog::record(
                $user,
                'logout',
                "Pengguna {$user->name} keluar dari sistem",
                $request
            );
        }

        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout Berhasil!'
        ], 200);
    }
}

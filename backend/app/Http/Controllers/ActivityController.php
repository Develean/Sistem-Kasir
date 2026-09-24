<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas pengguna
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->filled('role') && in_array($request->role, ['admin', 'kasir'])) {
            $query->where('user_role', $request->role);
        }

        // Filter Action
        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->orderBy('id', 'desc')->paginate($request->integer('per_page', 50));

        // Ringkasan Statistik
        $today = now()->toDateString();
        $stats = [
            'total'       => ActivityLog::count(),
            'today'       => ActivityLog::whereDate('created_at', $today)->count(),
            'kasir_count' => ActivityLog::where('user_role', 'kasir')->count(),
            'admin_count' => ActivityLog::where('user_role', 'admin')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats'  => $stats,
            'data'   => $logs->items(),
            'meta'   => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'total'        => $logs->total(),
            ]
        ], 200);
    }

    /**
     * Bersihkan log aktivitas lama (opsional)
     */
    public function clear(Request $request)
    {
        ActivityLog::truncate();

        // Catat aksi pembersihan
        ActivityLog::record($request->user(), 'system', 'Admin membersihkan riwayat activity logs', $request);

        return response()->json([
            'status'  => 'success',
            'message' => 'Seluruh riwayat aktivitas berhasil dibersihkan.'
        ], 200);
    }
}

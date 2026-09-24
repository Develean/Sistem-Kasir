<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper statis untuk mencatat log aktivitas
     */
    public static function record($user, string $action, string $description, $request = null): self
    {
        $userName = is_object($user) ? ($user->name ?? 'User') : (is_string($user) ? $user : 'Sistem');
        $userRole = is_object($user) ? ($user->role ?? 'kasir') : 'kasir';
        $userId   = is_object($user) ? ($user->id ?? null) : null;
        $ip       = $request ? $request->ip() : request()->ip();

        return self::create([
            'user_id'     => $userId,
            'user_name'   => $userName,
            'user_role'   => $userRole,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $ip,
        ]);
    }
}

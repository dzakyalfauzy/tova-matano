<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name', 'photo_url', 'divisi_name', 'points', 'message', 'joined_at', 'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'joined_at' => 'datetime',
    ];

    // Dipakai di halaman Anggota (semua) & Portal Keanggotaan Beranda (top N)
    public function scopeTopByPoints($query, int $limit = 10)
    {
        return $query->orderByDesc('points')->limit($limit);
    }
}

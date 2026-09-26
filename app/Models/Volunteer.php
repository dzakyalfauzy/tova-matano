<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Volunteer extends Model
{
    protected $fillable = [
        'registration_code', 'full_name', 'email', 'phone', 'address', 'motivation',
        'status', 'photo_url', 'submitted_at', 'reviewed_at', 'reviewed_by', 'notified_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Auto-generate kode pendaftaran unik saat data baru dibuat (Opsi D: cek status manual)
        static::creating(function (Volunteer $volunteer) {
            if (empty($volunteer->registration_code)) {
                $volunteer->registration_code = 'TMCC-' . strtoupper(Str::random(6));
            }
        });
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }
}

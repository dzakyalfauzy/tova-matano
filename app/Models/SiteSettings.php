<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Singleton — selalu akses lewat SiteSettings::first() atau ::firstOrCreate([], [...])
class SiteSettings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'address', 'phone', 'whatsapp', 'email',
        'instagram_url', 'facebook_url', 'tiktok_url', 'twitter_url',
    ];
}

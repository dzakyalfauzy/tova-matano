<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Singleton ?" selalu akses lewat SiteSettings::first() atau ::firstOrCreate([], [...])
class SiteSettings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'fixed_text',
        'address', 'phone', 'whatsapp', 'email',
        'instagram_url', 'facebook_url', 'tiktok_url', 'twitter_url',
        'about_hero_image_url', 'about_lake_image_url', 'org_hero_image_url', 'org_chart_image_url',
        'stat_sampah_terangkut', 'stat_sekolah_teredukasi',
    ];

    protected $casts = [
        'fixed_text' => 'array',
    ];

    public function getFixedText(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = is_array($this->fixed_text) ? $this->fixed_text : [];

        $text = $translations[$locale] ?? null;

        if ((! is_string($text) || trim($text) === '') && $locale !== 'id') {
            $text = $translations['id'] ?? null;
        }

        if (is_string($text) && trim($text) !== '') {
            return trim($text);
        }

        return null;
    }

    public function getHeroFixedTextAttribute(): ?string
    {
        return $this->getFixedText();
    }
}

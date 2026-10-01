<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title', 'subtitle', 'cta_label'];

    protected $fillable = [
        'image_url', 'cta_url', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'icon_url', 'order'];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->where('is_published', true)->latest('published_at');
    }
}

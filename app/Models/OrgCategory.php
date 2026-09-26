<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgCategory extends Model
{
    protected $fillable = ['name', 'description', 'display_type', 'order'];

    public function people(): HasMany
    {
        return $this->hasMany(OrgPerson::class)->orderBy('order');
    }
}

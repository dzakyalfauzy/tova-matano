<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgPerson extends Model
{
    protected $fillable = [
        'org_category_id', 'name', 'role_title', 'photo_url', 'divisi_name', 'order',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(OrgCategory::class, 'org_category_id');
    }
}

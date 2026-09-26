<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RunningText extends Model
{
    protected $fillable = ['text', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}

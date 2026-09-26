<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationChannel extends Model
{
    protected $fillable = ['bank_name', 'account_no', 'account_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}

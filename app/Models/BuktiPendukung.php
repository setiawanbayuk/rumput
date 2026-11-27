<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPendukung extends Model
{
    protected $guarded = [];
    protected $casts = ['ukuran' => 'integer'];
}

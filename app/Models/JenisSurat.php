<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'jenis',
        'assets',
        'detail',
        'persyaratan',
        'is_active'
    ];
}

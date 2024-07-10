<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skpd extends Model
{
    use HasFactory;

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_region', 'id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec', 'id');
    }
}

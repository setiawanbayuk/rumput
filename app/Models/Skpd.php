<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skpd extends Model
{
    use HasFactory;

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'id_region', 'id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'id_kec', 'id');
    }
}

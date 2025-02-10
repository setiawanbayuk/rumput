<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Regional extends Model
{
    use HasFactory;

    public function skpd(): HasOne
    {
        return $this->hasOne(Skpd::class, 'id_region', 'id');
    }
}

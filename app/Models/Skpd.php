<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skpd extends Model
{
    use HasFactory;

    public $primaryKey = 'id';

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_region', 'id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec', 'id');
    }

    /**
     * Relasi: SKPD (Kelurahan) memiliki banyak RT/RW.
     * FK: rt_rws.id_kel → skpds.id
     */
    public function rtrw(): HasMany
    {
        return $this->hasMany(RtRw::class, 'kode_kelurahan', 'id_region');
    }
}

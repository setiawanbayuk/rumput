<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RtRw extends Model
{
    protected $table = 'rt_rws';   // nama tabel
    protected $fillable = [
        'kode_kelurahan',
        'rw',
        'rt'];
    public $timestamps = false;    // karena di tabel kamu nggak ada created_at / updated_at

    /**
     * Relasi: RT/RW ini milik satu SKPD (kelurahan).
     * FK: rt_rws.id_kel → skpds.id
     */
    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class, 'kode_kelurahan', 'id_region');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(RtRw::class, 'kode_kelurahan', 'id_region');
    }

    /**
     * Relasi ke tabel user (jika RT/RW punya banyak user)
     */
    public function rw(): HasMany
    {
        return $this->hasMany(User::class, 'id_rw', 'rw');
    }

    public function rt(): HasMany
    {
        return $this->hasMany(User::class, 'id_rt', 'rt');
    }
}

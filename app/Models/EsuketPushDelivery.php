<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsuketPushDelivery extends Model
{
    protected $table = 'esuket_push_deliveries';

    protected $fillable = [
        'surat_pengajuan_id', 'push_device_id', 'event_name',
        'status', 'attempts', 'http_status', 'error_code', 'sent_at',
    ];

    protected $casts = ['sent_at' => 'datetime'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(EsuketPushDevice::class, 'push_device_id');
    }

    public function surat(): BelongsTo
    {
        return $this->belongsTo(SuratPengajuan::class, 'surat_pengajuan_id');
    }
}

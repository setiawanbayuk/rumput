<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsuketPushDelivery extends Model
{
    protected $table = 'esuket_push_deliveries';

    protected $fillable = [
        'surat_pengajuan_id', 'push_device_id', 'event_name',
        'status', 'attempts', 'http_status', 'error_code', 'sent_at',
    ];

    protected $casts = ['sent_at' => 'datetime'];
}

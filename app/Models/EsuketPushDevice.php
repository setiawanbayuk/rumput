<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsuketPushDevice extends Model
{
    protected $table = 'esuket_push_devices';

    protected $fillable = ['user_id', 'device_id', 'platform', 'fcm_token', 'token_hash'];

    protected $casts = ['fcm_token' => 'encrypted'];

    protected $hidden = ['fcm_token', 'token_hash'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

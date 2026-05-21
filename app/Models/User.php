<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'phone',
        'role_id',
        'id_instansi',
        'id_rw',
        'id_rt',
        'password',
        'foto',
    ];

    /**
     * Tambahan atribut otomatis saat model User dijadikan JSON.
     * Ini berguna agar mobile langsung mendapat URL lengkap foto profil.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'foto_url',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Accessor URL foto profil.
     * Database menyimpan path: profile/nama-file.jpg
     * Mobile membaca URL: http://domain/storage/profile/nama-file.jpg
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class, 'id_instansi', 'id');
    }

    public function user_role(): BelongsTo
    {
        return $this->belongsTo(User_role::class, 'role_id', 'id');
    }

    public function rw(): BelongsTo
    {
        return $this->belongsTo(RtRw::class, 'id_rw', 'rw');
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(RtRw::class, 'id_rt', 'rt');
    }
}
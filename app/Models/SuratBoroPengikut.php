<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratBoroPengikut extends Model
{
    use HasFactory;

    protected $fillable = [
        'boro_id',
        'nik',
        'nama',
        'gender',
        'gender_nm',
        'status_kwn',
        'status_kwn_nm',
        'umur',
        'hubungan'
    ];

}

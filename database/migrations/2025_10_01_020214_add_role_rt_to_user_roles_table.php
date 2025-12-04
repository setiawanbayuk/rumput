<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user_roles')->insert([
            'id'   => 8, // pastikan ID 8 belum dipakai
            'name' => 'RT',
        ]);
    }

    public function down(): void
    {
        DB::table('user_roles')->where('id', 8)->delete();
    }
};


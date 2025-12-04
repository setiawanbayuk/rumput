<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user_roles')->insert([
            'id'   => 9, // pastikan ID 8 belum dipakai
            'name' => 'Operator',
        ]);
    }

    public function down(): void
    {
        DB::table('user_roles')->where('id', 9)->delete();
    }
};


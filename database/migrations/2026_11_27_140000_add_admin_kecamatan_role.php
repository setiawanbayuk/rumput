<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $existing = DB::table('user_roles')->where('id', 10)->first();
        if ($existing) {
            if (mb_strtolower(trim((string) $existing->name)) !== 'admin kecamatan') {
                throw new RuntimeException('Role ID 10 sudah digunakan: tidak boleh menimpa role lama.');
            }
            return;
        }
        if (DB::table('user_roles')->where('name', 'Admin Kecamatan')->exists()) {
            throw new RuntimeException('Nama Admin Kecamatan sudah ada dengan ID berbeda. Periksa sebelum migration.');
        }
        DB::table('user_roles')->insert(['id' => 10, 'name' => 'Admin Kecamatan']);
    }

    public function down(): void
    {
        // Jaga akun yang sudah ada, tidak pernah menghapus role yang sudah terpakai.
        if (!DB::table('users')->where('role_id', 10)->exists()) {
            DB::table('user_roles')->where('id', 10)->where('name', 'Admin Kecamatan')->delete();
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rt_rws', function (Blueprint $table) {
            // Tambah kolom id_kel kalau belum ada
            if (!Schema::hasColumn('rt_rws', 'id_kel')) {
                $table->unsignedBigInteger('id_kel')->nullable()->after('id');
            }

            // Tambah foreign key ke tabel skpds
            $table->foreign('id_kel')
                ->references('id')
                ->on('skpds')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });

        // Langsung isi nilai id_kel berdasarkan kecocokan kode_kelurahan = id_region
        DB::statement("
            UPDATE rt_rws r
            JOIN skpds s ON s.id_region = r.kode_kelurahan
            SET r.id_kel = s.id
        ");
    }

    public function down(): void
    {
        Schema::table('rt_rws', function (Blueprint $table) {
            $table->dropForeign(['id_kel']);
            $table->dropColumn('id_kel');
        });
    }
};

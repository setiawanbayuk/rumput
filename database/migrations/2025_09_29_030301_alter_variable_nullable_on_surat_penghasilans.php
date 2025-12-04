<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_penghasilans', function (Blueprint $table) {
            // samakan tipe data dengan yang sudah ada, di sini TEXT
            $table->text('variable')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_penghasilans', function (Blueprint $table) {
            // rollback ke NOT NULL (atau default sebelumnya)
            $table->text('variable')->nullable(false)->change();
        });
    }
};

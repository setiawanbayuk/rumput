<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_templates', function (Blueprint $table) {
            // Kita ubah menjadi text dulu atau langsung json jika DB mendukung (MySQL 5.7+ / MariaDB 10.2+)
            $table->json('variable')->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_templates', function (Blueprint $table) {
            $table->text('variable')->change();
        });
    }
};

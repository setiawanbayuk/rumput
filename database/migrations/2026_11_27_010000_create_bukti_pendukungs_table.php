<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bukti_pendukungs', function (Blueprint $table) {
            $table->id();
            // Mendukung tabel surat_pengajuans dan tabel surat per jenis (legacy).
            $table->string('surat_table', 80);
            $table->unsignedBigInteger('surat_id');
            $table->string('path', 500);
            $table->string('nama_asli', 255)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('ukuran')->nullable();
            $table->timestamps();
            $table->index(['surat_table', 'surat_id'], 'bukti_pendukung_surat_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pendukungs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengajuans', function (Blueprint $table) {
            $table->id();

            // 1. Identitas Surat
            $table->string('jenis_surat'); // suket, skbn, skboro, skdom, skhsl, sktm, skusaha
            $table->string('nik', 16);
            $table->unsignedBigInteger('id_kel'); // Relasi ke ID Kelurahan/Instansi
            $table->integer('id_rw')->nullable();
            $table->integer('id_rt')->nullable();

            // 2. Penomoran (Biasanya diupdate saat verifikasi Kelurahan)
            $table->string('kd_jenis_surat')->default('0');
            $table->integer('no_urut_surat')->default(0);
            $table->string('tahun', 4);
            $table->date('tgl_surat');

            // 3. Konten Umum
            $table->string('peruntukan')->nullable();
            $table->string('kepada')->nullable();

            // 4. Status Pengajuan
            // 0: Draft/Warga, 1: Verifikasi RT/RW, 2: Verifikasi Sekkel, 3: TTE Lurah, 4: Selesai
            $table->tinyInteger('status')->default(0);

            // 5. Manajemen File
            $table->string('pengantar')->nullable(); // Path foto surat pengantar dari RT
            $table->string('file')->nullable();      // Path hasil PDF yang sudah ditandatangani

            // 6. Data Dinamis (Kunci Utama)
            // Kolom ini akan menyimpan data unik tiap surat (misal: nama_usaha, penghasilan, tgl_kematian, dll)
            $table->json('variable')->nullable();

            $table->timestamps();

            // Indexing untuk mempercepat pencarian
            $table->index(['nik', 'jenis_surat', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuans');
    }
};

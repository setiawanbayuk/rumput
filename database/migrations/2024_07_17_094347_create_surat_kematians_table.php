<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_kematians', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nama_pelapor');
            $table->string('nik_pelapor');
            $table->string('kk_pelapor');
            $table->string('kewarganegaraan_pelapor');
            $table->string('no_dokumen_perjalanan')->nullable();
            $table->string('nama_saksi1');
            $table->string('nik_saksi1');
            $table->string('kk_saksi1');
            $table->string('kewarganegaraan_saksi1');
            $table->string('nama_saksi2');
            $table->string('nik_saksi2');
            $table->string('kk_saksi2');
            $table->string('kewarganegaraan_saksi2');
            $table->string('nama_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->string('tempat_lhr_ayah')->nullable();
            $table->string('tgl_lhr_ayah')->nullable();
            $table->string('kewarganegaraan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->string('tempat_lhr_ibu')->nullable();
            $table->string('tgl_lhr_ibu')->nullable();
            $table->string('kewarganegaraan_ibu')->nullable();
            $table->string('nik');
            $table->string('nama');
            $table->string('tgl_kematian');
            $table->time('jam_kematian');
            $table->string('sebab_kematian');
            $table->string('sebab_kematian_nm');
            $table->string('tempat_kematian');
            $table->string('yang_menerangkan');
            $table->string('status');
            $table->string('file')->nullable();
            $table->string('pengantar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_kematians');
    }
};

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
        Schema::create('surat_kelahirans', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nama_pelapor');
            $table->string('nik_pelapor');
            $table->string('kk_pelapor');
            $table->string('kewarganegaraan_pelapor');
            $table->string('kewarganegaraan_pelapor_nm');
            $table->string('no_dokumen_perjalanan')->nullable();
            $table->string('nama_saksi1');
            $table->string('nik_saksi1');
            $table->string('kk_saksi1');
            $table->string('kewarganegaraan_saksi1');
            $table->string('kewarganegaraan_saksi1_nm');
            $table->string('nama_saksi2');
            $table->string('nik_saksi2');
            $table->string('kk_saksi2');
            $table->string('kewarganegaraan_saksi2');
            $table->string('kewarganegaraan_saksi2_nm');
            $table->string('nama_ayah');
            $table->string('nik_ayah');
            $table->string('kk_ayah');
            $table->string('tempat_lhr_ayah');
            $table->string('tgl_lhr_ayah');
            $table->string('kewarganegaraan_ayah');
            $table->string('kewarganegaraan_ayah_nm');
            $table->string('nama_ibu');
            $table->string('nik_ibu');
            $table->string('kk_ibu');
            $table->string('tempat_lhr_ibu');
            $table->string('tgl_lhr_ibu');
            $table->string('kewarganegaraan_ibu');
            $table->string('kewarganegaraan_ibu_nm');
            $table->string('nama_anak');
            $table->string('gender_anak');
            $table->string('gender_anak_nm');
            $table->string('tempat_dilahirkan_anak');
            $table->string('tempat_kelahiran_anak');
            $table->string('hari_lhr_anak');
            $table->string('tgl_lhr_anak');
            $table->time('jam_lhr_anak');
            $table->string('jenis_klhr_anak');
            $table->string('klhr_ke_anak');
            $table->string('penolong_klhr_anak');
            $table->string('bb_anak');
            $table->string('tb_anak');
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
        Schema::dropIfExists('surat_kelahirans');
    }
};

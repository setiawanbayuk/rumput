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
        Schema::create('surat_penghasilans', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nik');
            $table->text('peruntukan');
            $table->string('penghasilan');
            $table->string('terbilang');
            $table->string('kepada');
            $table->string('kepada_tempat_lhr');
            $table->string('kepada_tgl_lhr');
            $table->string('kepada_gender');
            $table->string('kepada_gender_nm');
            $table->string('kepada_sekolah');
            $table->string('kepada_alamat_sekolah');
            $table->string('kepada_kelas');
            $table->string('kepada_hubungan');
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
        Schema::dropIfExists('surat_penghasilans');
    }
};

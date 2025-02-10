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
        Schema::create('surat_sktms', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nik');
            $table->text('peruntukan');
            $table->string('kategori');
            $table->string('kepada')->nullable();
            $table->string('kepada_tempat_lhr')->nullable();
            $table->string('kepada_tgl_lhr')->nullable();
            $table->string('kepada_gender')->nullable();
            $table->string('kepada_sekolah')->nullable();
            $table->string('kepada_alamat_sekolah')->nullable();
            $table->string('kepada_kelas')->nullable();
            $table->string('kepada_hubungan')->nullable();
            $table->string('jenis');
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
        Schema::dropIfExists('surat_sktms');
    }
};

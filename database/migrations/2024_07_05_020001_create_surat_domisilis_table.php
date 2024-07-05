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
        Schema::create('surat_domisilis', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nik');
            $table->text('nama_perusahaan')->nullable();
            $table->text('status_bangunan')->nullable();
            $table->text('jumlah_karyawan')->nullable();
            $table->text('alamat_domisili');
            $table->text('peruntukan');
            $table->string('kepada');
            $table->date('tgl_berlaku')->nullable();
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
        Schema::dropIfExists('surat_domisilis');
    }
};

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
        Schema::create('surat_boros', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nik');
            $table->string('prov_boro');
            $table->string('prov_boro_nm');
            $table->string('kabko_boro');
            $table->string('kabko_boro_nm');
            $table->string('kec_boro');
            $table->string('kec_boro_nm');
            $table->string('kel_boro');
            $table->string('kel_boro_nm');
            $table->date('tgl_awal');
            $table->date('tgl_akhir');
            $table->text('peruntukan');
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
        Schema::dropIfExists('surat_boros');
    }
};

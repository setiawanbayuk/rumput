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
        Schema::create('surat_usahas', function (Blueprint $table) {
            $table->id();
            $table->integer('id_kel');
            $table->string('kd_jenis_surat');
            $table->integer('no_urut_surat');
            $table->date('tgl_surat');
            $table->string('nik');
            $table->string('nama_usaha');
            $table->text('alamat_usaha');
            $table->text('peruntukan');
            $table->string('kepada');
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
        Schema::dropIfExists('surat_usahas');
    }
};

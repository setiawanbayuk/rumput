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
        Schema::create('skpds', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('id_region');
            $table->string('instansi_kode');
            $table->string('instansi_telp');
            $table->string('instansi_fax');
            $table->string('instansi_alamat');
            $table->string('instansi_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skpds');
    }
};

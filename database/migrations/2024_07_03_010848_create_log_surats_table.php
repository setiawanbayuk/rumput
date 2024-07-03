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
        Schema::create('log_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('tabel_surat');
            $table->string('nama_surat');
            $table->string('id_surat');
            $table->string('status_surat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_surats');
    }
};

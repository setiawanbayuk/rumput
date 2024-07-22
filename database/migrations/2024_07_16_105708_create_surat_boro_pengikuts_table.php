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
        Schema::create('surat_boro_pengikuts', function (Blueprint $table) {
            $table->id();
            $table->integer('boro_id');
            $table->string('nik');
            $table->string('nama');
            $table->string('gender');
            $table->string('gender_nm');
            $table->string('umur');
            $table->string('status_kwn');
            $table->string('status_kwn_nm');
            $table->string('hubungan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_boro_pengikuts');
    }
};

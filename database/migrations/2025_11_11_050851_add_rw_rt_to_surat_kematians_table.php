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
        Schema::table('surat_kematians', function (Blueprint $table) {
            $table->integer('id_rw')->nullable()->after('id_kel');
            $table->integer('id_rt')->nullable()->after('id_rw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_kematians', function (Blueprint $table) {
            $table->dropColumn(['id_rw', 'id_rt']);
        });
    }
};

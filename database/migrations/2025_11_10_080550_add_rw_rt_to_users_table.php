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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id_rw')) {
                $table->integer('id_rw')->nullable()->after('id_instansi');
            }
            if (!Schema::hasColumn('users', 'id_rt')) {
                $table->integer('id_rt')->nullable()->after('id_rw');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_rw')) {
                $table->dropColumn('id_rw');
            }
            if (Schema::hasColumn('users', 'id_rt')) {
                $table->dropColumn('id_rt');
            }
        });
    }
};

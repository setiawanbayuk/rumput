<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->string('push_route')->nullable()->after('jenis');
        });

        foreach ([
            'skbn' => 'esuket-skbn-progress',
            'skdom' => 'esuket-skdom-progress',
            'sktm' => 'esuket-sktm-progress',
            'skusaha' => 'esuket-sku-progress',
            'suket' => 'esuket-sk-progress',
            'skboro' => 'esuket-skb-progress',
            'skhsl' => 'esuket-skh-progress',
        ] as $jenis => $route) {
            DB::table('jenis_surats')->where('jenis', $jenis)->update(['push_route' => $route]);
        }
    }

    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('push_route');
        });
    }
};

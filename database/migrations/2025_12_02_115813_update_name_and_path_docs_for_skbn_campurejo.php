<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update path_docs untuk Kelurahan ID 12 (Campurejo) khusus SKBN
        DB::table('surat_templates')
            ->where('id_kel', 12)
            ->where('jenis', 'skbn')
            ->update([
                'name' => 'SKBN CAMPUREJO',
                'path_docs' => 'templates/SKBN_CAMPUREJO.docx'
            ]);
    }

    public function down(): void
    {
        // OPTIONAL: Kembalikan ke null atau isi lama kalau perlu
        DB::table('surat_templates')
            ->where('id_kel', 12)
            ->where('jenis', 'skbn')
            ->update([
                'name' => 'AA',
                'path_docs' => 'templates/4HaZQFPsLDZ1VdYGDrplUHg8TaTWRpIskFmEKl8M.docx'   // atau isi file hash lama kalau masih ingat
            ]);
    }
};

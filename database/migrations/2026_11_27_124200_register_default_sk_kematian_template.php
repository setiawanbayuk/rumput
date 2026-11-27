<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mendaftarkan file DOCX SKKEMATIAN yang SUDAH dimiliki pengguna.
        // Tidak membuat, mengubah, menimpa, atau menghapus file template apa pun.
        // id_kel=0 adalah default lintas kelurahan di TemplateController::index().
        $alreadyVisible = DB::table('surat_templates')
            ->where('jenis', 'skkematian')
            ->where('state', 'default')
            ->whereIn('id_kel', [0, 67])
            ->exists();

        if (!$alreadyVisible) {
            DB::table('surat_templates')->insert([
                'id_kel' => 0,
                'name' => 'Surat Keterangan Kematian',
                'path_docs' => 'templates/SKKEMATIAN.docx',
                'jenis' => 'skkematian',
                'state' => 'default',
                'variable' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus entri template pada rollback agar entri
        // default/custom yang telah diatur admin tetap aman.
    }
};

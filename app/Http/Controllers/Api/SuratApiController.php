<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuratPengajuan;
use App\Models\Resident;
use App\Models\Log_surat;
use Illuminate\Support\Facades\Storage;

class SuratApiController extends Controller
{
    /**
     * Store a universal letter request into a single table.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'jenis_surat' => ['required', 'string'],
            'nik'         => ['required', 'digits:16'],
            'peruntukan'  => ['required', 'string', 'max:255'],
            'kepada'      => ['required', 'string', 'max:255'],
            'pengantar'   => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = auth()->user();
                $jenis = strtolower($request->jenis_surat);
                $year = date('Y');

                // 2. Cek Data Penduduk
                $resident = Resident::where('nik', $request->nik)->first();
                if (!$resident) {
                    throw new \Exception("NIK tidak terdaftar dalam pangkalan data penduduk.");
                }

                // 3. Proses File Pengantar
                $file = $request->file('pengantar');
                $fileName = $file->hashName();
                // Folder dinamis sesuai jenis surat agar penyimpanan rapi
                $savePath = "public/pengantar/{$year}/{$jenis}";
                $file->storeAs($savePath, $fileName);
                $fileUrl = "/storage/pengantar/{$year}/{$jenis}/{$fileName}";

                // 4. Pisahkan Data Statis vs Data Dinamis (JSON)
                $allInput = $request->all();

                // Daftar kolom yang ada di tabel 'surat_pengajuans'
                $mainColumns = [
                    'jenis_surat', 'nik', 'peruntukan', 'kepada',
                    'id_kel', 'id_rw', 'id_rt', 'tahun', 'tgl_surat'
                ];

                // Ambil data selain kolom utama untuk dimasukkan ke JSON 'variable'
                // Kita kecualikan juga file pengantar dan token dari JSON
                $variableData = array_diff_key(
                    $allInput,
                    array_flip(array_merge($mainColumns, ['pengantar', 'token', '_method']))
                );

                // 5. Simpan ke Database
                $surat = SuratPengajuan::create([
                    'jenis_surat'    => $jenis,
                    'nik'            => $request->nik,
                    'id_kel'         => $user->id_instansi,
                    'id_rw'          => $user->id_rw,
                    'id_rt'          => $user->id_rt,
                    'tahun'          => $year,
                    'tgl_surat'      => now(),
                    'peruntukan'     => $request->peruntukan,
                    'kepada'         => $request->kepada,
                    'status'         => 0, // Status awal: Draft/Pengajuan Warga
                    'pengantar'      => $fileUrl,
                    'variable'       => $variableData, // Otomatis tersimpan sebagai JSON
                ]);

                // 6. Catat Log Surat
                Log_surat::create([
                    'nik'          => $request->nik,
                    'tabel_surat'  => 'surat_pengajuans',
                    'nama_surat'   => strtoupper($jenis),
                    'id_surat'     => $surat->id,
                    'status_surat' => 0,
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Pengajuan surat berhasil dikirim!',
                    'data'    => $surat
                ], 201);
            });

        } catch (\Exception $e) {
            // Jika terjadi error, hapus file yang mungkin sudah terlanjur di-upload
            if (isset($fileUrl)) {
                Storage::delete(str_replace('/storage/', 'public/', $fileUrl));
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}

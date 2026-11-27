<?php

namespace App\Traits;

use App\Models\BuktiPendukung;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

trait HasBuktiPendukung
{
    public function buktiPendukung(): HasMany
    {
        return $this->hasMany(BuktiPendukung::class, 'surat_id')
            ->where('surat_table', $this->getTable());
    }

    protected static function bootHasBuktiPendukung(): void
    {
        static::saved(function ($surat) {
            $request = request();
            $files = $request->file('bukti_pendukung', []);
            if (!is_array($files)) {
                $files = [];
            }
            $editTarget = $request->attributes->get('bukti_pendukung_edit_target');
            $isTarget = is_array($editTarget) && $editTarget === [$surat->getTable(), (string) $surat->getKey()];
            $idsHapus = $isTarget ? $request->attributes->get('bukti_pendukung_hapus_valid', []) : [];
            $hapusLegacy = $isTarget && $request->attributes->get('bukti_pendukung_hapus_legacy_valid', false);
            if (!$files && !$idsHapus && !$hapusLegacy) {
                return;
            }

            // Simpan hanya satu kali, meskipun model yang sama di-save beberapa kali per request.
            $key = 'bukti_pendukung_saved_' . $surat->getTable() . '_' . $surat->getKey();
            if ($request->attributes->get($key)) {
                return;
            }
            $request->attributes->set($key, true);

            $skipFirst = $request->attributes->get('bukti_pendukung_primary_to_pengantar', false);
            foreach ($files as $index => $file) {
                if ($skipFirst && $index === 0) {
                    // File pertama disimpan oleh alur lama pada kolom pengantar.
                    continue;
                }
                if (!$file || !$file->isValid()) {
                    continue;
                }

                // File tambahan disimpan PRIVAT dan hanya dapat dibaca lewat route terotorisasi.
                $folder = 'bukti_pendukung/' . date('Y') . '/' . $surat->getTable() . '/' . $surat->getKey();
                $namaAsli = mb_substr($file->getClientOriginalName(), 0, 255);
                $mimeType = $file->getMimeType();
                $ukuran = $file->getSize();
                $path = Storage::disk('local')->putFile($folder, $file);
                if (!$path) {
                    throw new \RuntimeException('Gagal menyimpan Bukti Pendukung.');
                }
                $surat->buktiPendukung()->create([
                    'surat_table' => $surat->getTable(),
                    'path' => $path,
                    'nama_asli' => $namaAsli,
                    'mime_type' => $mimeType,
                    'ukuran' => $ukuran,
                ]);
            }

            // Penghapusan hanya diproses jika middleware sudah mengotorisasi
            // ID milik surat ini dan memeriksa status serta minimal satu bukti.
            $hapusPaths = [];
            if ($idsHapus) {
                $buktiHapus = $surat->buktiPendukung()->whereIn('id', $idsHapus)->get();
                foreach ($buktiHapus as $bukti) {
                    $hapusPaths[] = $bukti->path;
                    $bukti->delete();
                }
            }
            $hapusPengantarPath = null;
            if ($hapusLegacy && filled($surat->pengantar)) {
                $lama = ltrim(str_replace('\\', '/', (string) parse_url($surat->pengantar, PHP_URL_PATH)), '/');
                if (preg_match('~^(?:(?:public/storage|storage|public)/)?(pengantar/.+)$~', $lama, $m)
                    && !preg_match('~(?:^|/)\.{1,2}(?:/|$)~', $m[1])) {
                    $hapusPengantarPath = $m[1];
                }
                // Query builder menghindari pemanggilan ulang event saved().
                DB::table($surat->getTable())->where('id', $surat->getKey())->update(['pengantar' => null]);
                $surat->setAttribute('pengantar', null);
            }
            if ($hapusPaths || $hapusPengantarPath) {
                $removeFiles = static function () use ($hapusPaths, $hapusPengantarPath): void {
                    foreach ($hapusPaths as $path) {
                        Storage::disk('local')->delete($path);
                    }
                    if ($hapusPengantarPath) {
                        Storage::disk('public')->delete($hapusPengantarPath);
                    }
                };
                DB::afterCommit($removeFiles);
            }

        });
    }
}

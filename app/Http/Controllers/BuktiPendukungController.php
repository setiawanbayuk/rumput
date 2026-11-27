<?php

namespace App\Http\Controllers;

use App\Models\BuktiPendukung;
use App\Models\Skpd;
use App\Models\SuratBoro;
use App\Models\SuratDomisili;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\SuratKeterangan;
use App\Models\SuratPengajuan;
use App\Models\SuratPenghasilan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratUsaha;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuktiPendukungController extends Controller
{
    private const SURAT_MODELS = [
        'pengajuan' => SuratPengajuan::class,
        'suket' => SuratKeterangan::class,
        'skbn' => SuratSkbn::class,
        'sktm' => SuratSktm::class,
        'skdom' => SuratDomisili::class,
        'skhsl' => SuratPenghasilan::class,
        'skusaha' => SuratUsaha::class,
        'skboro' => SuratBoro::class,
        'skkelahiran' => SuratKelahiran::class,
        'skkematian' => SuratKematian::class,
    ];

    private function resolveSurat(Request $request, string $sumber, int $id): Model
    {
        abort_unless(isset(self::SURAT_MODELS[$sumber]), 404);
        $surat = self::SURAT_MODELS[$sumber]::findOrFail($id);
        $user = $request->user();
        abort_unless($user, 403);
        $role = (int) $user->role_id;

        if ($role === 7) {
            return $surat;
        }
        if ($role === 10) {
            abort_unless(strtolower((string) ($surat->jenis_surat ?? $sumber)) === 'sktm' && $sumber === 'pengajuan', 403);
        }
        if ($role === 2) {
            abort_unless((string) $surat->nik === (string) $user->nik && filled($user->nik), 403);
            return $surat;
        }
        abort_unless(in_array($role, [1, 3, 4, 5, 6, 8, 9, 10], true), 403);

        $idInstansi = (int) ($user->id_instansi ?? 0);
        $idKel = (int) ($surat->id_kel ?? 0);
        abort_unless($idInstansi > 0 && $idKel > 0, 403);

        if (in_array($role, [5, 6, 10], true)) {
            // Mengikuti akses wilayah kecamatan pada SuratAdminController.
            $instansi = Skpd::find($idInstansi);
            $kelurahan = Skpd::find($idKel);
            $regions = array_values(array_filter([
                trim((string) ($instansi->id_kec ?? '')),
                substr_count(trim((string) ($instansi->id_region ?? '')), '.') === 2
                    ? trim((string) $instansi->id_region) : '',
            ]));
            abort_unless($instansi && $kelurahan && $regions && in_array((string) $kelurahan->id_kec, $regions, true), 403);
        } else {
            abort_unless($idInstansi === $idKel, 403);
        }

        if ($role === 8) {
            if (filled($user->id_rw)) {
                abort_unless((string) $surat->id_rw === (string) $user->id_rw, 403);
            }
            if (filled($user->id_rt)) {
                abort_unless((string) $surat->id_rt === (string) $user->id_rt, 403);
            }
        }

        return $surat;
    }

    public function show(Request $request, string $sumber, int $id)
    {
        $surat = $this->resolveSurat($request, $sumber, $id);
        $lampiran = $surat->buktiPendukung()->orderBy('id')->get();

        // Fragmen galeri untuk Preview di halaman yang sama. Tetap melalui resolveSurat()
        // sehingga validasi role dan wilayah sama dengan halaman galeri sebelumnya.
        if ($request->boolean('fragment')) {
            return view('bukti-pendukung.galeri', compact('surat', 'lampiran', 'sumber'));
        }

        return view('bukti-pendukung.show', compact('surat', 'lampiran', 'sumber'));
    }

    public function apiIndex(Request $request, int $id)
    {
        $surat = $this->resolveSurat($request, 'pengajuan', $id);
        $files = [];
        if (filled($surat->pengantar)) {
            $files[] = [
                'id' => null,
                'nama' => 'Bukti Pendukung 1',
                'url' => route('api.bukti-pendukung.legacy-file', ['id' => $surat->id]),
                'legacy' => true,
            ];
        }
        foreach ($surat->buktiPendukung()->orderBy('id')->get() as $bukti) {
            $files[] = [
                'id' => $bukti->id,
                'nama' => $bukti->nama_asli,
                'mime_type' => $bukti->mime_type,
                'ukuran' => $bukti->ukuran,
                'url' => route('api.bukti-pendukung.file', ['id' => $surat->id, 'file' => $bukti->id]),
                'legacy' => false,
            ];
        }

        return response()->json(['status' => 'success', 'jumlah' => count($files), 'data' => $files]);
    }

    public function apiFile(Request $request, int $id, int $file)
    {
        return $this->file($request, 'pengajuan', $id, $file);
    }

    /**
     * Bukti pertama pada pengajuan lama disimpan di kolom `pengantar` dan
     * berada di storage/app/public. Sajikan lewat route berizin agar tidak
     * bergantung pada symbolic link public/storage maupun APP_URL.
     */
    public function legacyFile(Request $request, string $sumber, int $id)
    {
        $surat = $this->resolveSurat($request, $sumber, $id);
        $storedPath = trim((string) ($surat->pengantar ?? ''));
        abort_if($storedPath === '', 404);

        // Kolom lama lazimnya berisi /storage/pengantar/...; adaptasi path saja,
        // tanpa mengubah isi database ataupun memindahkan file lama.
        $urlPath = parse_url($storedPath, PHP_URL_PATH);
        abort_unless(is_string($urlPath) && $urlPath !== '', 404);
        $urlPath = ltrim(str_replace('\\', '/', rawurldecode($urlPath)), '/');

        abort_unless(preg_match('~^(?:(?:public/storage|storage|public)/)?(pengantar/.+)$~', $urlPath, $matches) === 1, 404);
        $relativePath = $matches[1];
        abort_if(str_contains($relativePath, "\0")
            || preg_match('~(?:^|/)\.{1,2}(?:/|$)~', $relativePath) === 1, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($relativePath), 404);

        $mime = $disk->mimeType($relativePath);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp', 'image/x-ms-bmp', 'application/pdf'];
        abort_unless(in_array($mime, $allowed, true), 415);

        return response()->file($disk->path($relativePath), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function apiLegacyFile(Request $request, int $id)
    {
        return $this->legacyFile($request, 'pengajuan', $id);
    }

    public function file(Request $request, string $sumber, int $id, int $file)
    {
        $surat = $this->resolveSurat($request, $sumber, $id);
        $bukti = $surat->buktiPendukung()->whereKey($file)->firstOrFail();
        $disk = Storage::disk('local');
        abort_unless($disk->exists($bukti->path), 404);

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp', 'application/pdf'];
        $mime = in_array($bukti->mime_type, $allowed, true) ? $bukti->mime_type : 'application/octet-stream';
        return response()->file($disk->path($bukti->path), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

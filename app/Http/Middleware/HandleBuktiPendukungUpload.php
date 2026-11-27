<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\SuratPengajuan;
use App\Models\SuratKeterangan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratDomisili;
use App\Models\SuratPenghasilan;
use App\Models\SuratUsaha;
use App\Models\SuratBoro;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\Skpd;
use Symfony\Component\HttpFoundation\Response;

class HandleBuktiPendukungUpload
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = (string) optional($request->route())->getName();
        $jenis = '(suket|skbn|sktm|skdom|skhsl|skusaha|skboro|skkelahiran|skkematian)';
        $legacyWeb = preg_match('/^' . $jenis . '\.(store|save|update|updatewarga)$/', $route) === 1;
        $centralWeb = in_array($route, ['admin.surat.store', 'admin.surat.update'], true);
        $apiCentral = $request->is('api/surat/store') || $request->is('api/surat/*/revisi') || $request->is('api/surat/*/perbaiki');
        $apiLegacy = preg_match('#^api/(suket|skbn|sktm|skdom|skhsl|skusaha|skboro|skelahiran|skkematian)/?$#', $request->path()) === 1;

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)
            || !($legacyWeb || $centralWeb || $apiCentral || $apiLegacy)) {
            return $next($request);
        }

        // Baca FileBag langsung SEBELUM Request::allFiles() meng-cache daftar file.
        // Ini memastikan adapter pengantar terlihat oleh validasi dan controller lama.
        $files = $request->files->get('bukti_pendukung', []);
        if ($files && !is_array($files)) {
            throw ValidationException::withMessages(['bukti_pendukung' => 'Bukti Pendukung harus dikirim sebagai kumpulan file.']);
        }

        $isCreate = $route === 'admin.surat.store'
            || preg_match('/^' . $jenis . '\.(store|save)$/', $route) === 1
            || $apiLegacy
            || ($request->is('api/surat/store') && !$request->filled('pengajuan_id')
                && !$request->filled('id_pengajuan') && !$request->filled('surat_pengajuan_id')
                && !$request->filled('id_surat') && !$request->filled('surat_id'));

        // Adapter CREATE: gunakan file pertama sebagai pengantar legacy, bukan memindahkan semua foto.
        if ($isCreate && !$request->files->has('pengantar') && is_array($files) && isset($files[0])) {
            $request->files->set('pengantar', $files[0]);
            $request->attributes->set('bukti_pendukung_primary_to_pengantar', true);
        }

        // Ubah/hapus hanya pada form edit sebelum diserahkan ke Sekkel.
        // Pemeriksaan dilakukan di server, bukan hanya menyembunyikan tombol X.
        $idsHapus = $request->input('bukti_pendukung_hapus', []);
        $hapusLegacy = $request->boolean('bukti_pendukung_hapus_legacy');
        $adaPerubahanBukti = $request->hasFile('bukti_pendukung') || $request->hasFile('pengantar')
            || $request->exists('bukti_pendukung_hapus') || $request->exists('bukti_pendukung_hapus_legacy');

        if (!$isCreate && $adaPerubahanBukti) {
            $model = $this->resolveEditModel($request, $route);
            if (!$model) {
                throw ValidationException::withMessages(['bukti_pendukung' => 'Pengajuan yang akan diedit tidak ditemukan.']);
            }
            $belumKeSekkel = in_array((int) $model->status, [0, 1], true);
            $memintaHapus = $request->exists('bukti_pendukung_hapus') || $request->exists('bukti_pendukung_hapus_legacy');
            // Revisi surat ditolak di API boleh MENAMBAH berkas, tetapi tidak boleh menghapus
            // berkas setelah surat pernah naik ke Sekkel; controller API memvalidasi status ditolak.
            $revisiApi = $request->is('api/surat/*/revisi') || $request->is('api/surat/*/perbaiki')
                || $request->is('api/surat/store');
            if (!$belumKeSekkel && ($memintaHapus || !$revisiApi)) {
                throw ValidationException::withMessages(['bukti_pendukung' => 'Bukti Pendukung tidak dapat dihapus setelah surat diteruskan ke Sekkel atau proses TTE.']);
            }
            // Middleware group API berjalan sebelum auth:sanctum pada route.
            $user = $request->is('api/*') ? $request->user('sanctum') : $request->user();
            $role = (int) ($user->role_id ?? 0);
            if (!$user || !in_array($role, [1, 2, 3, 4, 5, 6, 7, 8, 9], true)) {
                throw ValidationException::withMessages(['bukti_pendukung' => 'Anda tidak memiliki izin mengubah Bukti Pendukung.']);
            }
            if ($role === 2 &&
                (blank($user->nik) || (string) $user->nik !== (string) $model->nik)) {
                throw ValidationException::withMessages(['bukti_pendukung' => 'Anda tidak dapat mengubah Bukti Pendukung milik pengguna lain.']);
            }
            if (!in_array($role, [2, 7], true)) {
                $idInstansi = (int) ($user->id_instansi ?? 0);
                $idKel = (int) ($model->id_kel ?? 0);
                if ($idInstansi <= 0 || $idKel <= 0) {
                    throw ValidationException::withMessages(['bukti_pendukung' => 'Surat berada di luar wilayah Anda.']);
                }
                if (in_array($role, [5, 6], true)) {
                    $instansi = Skpd::find($idInstansi);
                    $kelurahan = Skpd::find($idKel);
                    $regions = array_values(array_filter([
                        trim((string) ($instansi->id_kec ?? '')),
                        substr_count(trim((string) ($instansi->id_region ?? '')), '.') === 2
                            ? trim((string) $instansi->id_region) : '',
                    ]));
                    if (!$instansi || !$kelurahan || !$regions ||
                        !in_array((string) $kelurahan->id_kec, $regions, true)) {
                        throw ValidationException::withMessages(['bukti_pendukung' => 'Surat berada di luar wilayah Anda.']);
                    }
                } elseif ($idInstansi !== $idKel) {
                    throw ValidationException::withMessages(['bukti_pendukung' => 'Surat berada di luar wilayah Anda.']);
                }
                if ($role === 8 && ((filled($user->id_rw) && (string) $user->id_rw !== (string) $model->id_rw) ||
                    (filled($user->id_rt) && (string) $user->id_rt !== (string) $model->id_rt))) {
                    throw ValidationException::withMessages(['bukti_pendukung' => 'Surat berada di luar wilayah Anda.']);
                }
            }

            if ($hapusLegacy && $request->hasFile('pengantar')) {
                throw ValidationException::withMessages(['bukti_pendukung_hapus_legacy' => 'Tidak dapat menghapus dan mengganti lampiran utama lewat field lama pada permintaan yang sama.']);
            }

            Validator::make($request->all(), [
                'bukti_pendukung_hapus' => ['sometimes', 'array'],
                'bukti_pendukung_hapus.*' => ['required', 'integer', 'distinct', 'min:1'],
                'bukti_pendukung_hapus_legacy' => ['sometimes', 'boolean'],
            ])->validate();
            $idsHapus = array_map('intval', $request->input('bukti_pendukung_hapus', []));
            $matching = $model->buktiPendukung()->whereIn('id', $idsHapus)->count();
            if ($matching !== count($idsHapus) || ($hapusLegacy && blank($model->pengantar))) {
                throw ValidationException::withMessages(['bukti_pendukung_hapus' => 'Bukti Pendukung yang dipilih untuk dihapus tidak valid.']);
            }
            $remaining = $model->buktiPendukung()->count()
                + (filled($model->pengantar) ? 1 : 0)
                - $matching - ($hapusLegacy ? 1 : 0)
                + count($files ?: []);
            if ($remaining < 1) {
                throw ValidationException::withMessages(['bukti_pendukung' => 'Minimal satu Bukti Pendukung harus tersedia.']);
            }
            $request->attributes->set('bukti_pendukung_edit_target', [$model->getTable(), (string) $model->getKey()]);
            $request->attributes->set('bukti_pendukung_hapus_valid', $idsHapus);
            $request->attributes->set('bukti_pendukung_hapus_legacy_valid', $hapusLegacy);
        }

        Validator::make($request->all(), [
            'bukti_pendukung' => ['sometimes', 'array', 'min:1', 'max:20'],
            'bukti_pendukung.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,bmp,pdf', 'max:5120'],
            'pengantar' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp,bmp,pdf', 'max:5120'],
        ], [
            'bukti_pendukung.*.mimes' => 'Format Bukti Pendukung harus JPG, PNG, WEBP, BMP, atau PDF.',
            'bukti_pendukung.*.max' => 'Setiap file Bukti Pendukung maksimal 5 MB.',
        ])->validate();

        if ($isCreate && !$request->hasFile('pengantar') && !$request->hasFile('bukti_pendukung')) {
            throw ValidationException::withMessages([
                'bukti_pendukung' => 'Bukti Pendukung wajib diunggah minimal 1 file.',
            ]);
        }

        return $next($request);
    }
    private function resolveEditModel(Request $request, string $route)
    {
        $id = $request->route('id');
        // Endpoint API /surat/store menerima id lama melalui body saat revisi.
        if (!$id && $request->is('api/surat/store')) {
            foreach (['pengajuan_id', 'id_pengajuan', 'surat_pengajuan_id', 'id_surat', 'surat_id'] as $field) {
                if ($request->filled($field)) {
                    $id = $request->input($field);
                    break;
                }
            }
        }
        if (!$id || !ctype_digit((string) $id)) {
            return null;
        }
        if (str_starts_with($route, 'admin.surat.') || $request->is('api/surat/*/revisi')
            || $request->is('api/surat/*/perbaiki') || $request->is('api/surat/store')) {
            return SuratPengajuan::find((int) $id);
        }
        $jenis = explode('.', $route, 2)[0];
        $models = [
            'suket' => SuratKeterangan::class, 'skbn' => SuratSkbn::class,
            'sktm' => SuratSktm::class, 'skdom' => SuratDomisili::class,
            'skhsl' => SuratPenghasilan::class, 'skusaha' => SuratUsaha::class,
            'skboro' => SuratBoro::class, 'skkelahiran' => SuratKelahiran::class,
            'skkematian' => SuratKematian::class,
        ];
        return isset($models[$jenis]) ? $models[$jenis]::find((int) $id) : null;
    }

}

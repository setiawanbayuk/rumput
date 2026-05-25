<?php

namespace App\Http\Controllers;

use App\Models\Log_surat;
use App\Models\Pejabat;
use App\Models\Resident;
use App\Models\Skpd;
use App\Models\SuratPengajuan;
use App\Traits\GetNoSurat;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VerifySuratController extends Controller
{
    use GetNoSurat;

    public function show(string $jenis, int $id)
    {
        $jenis = strtolower(trim($jenis));

        // Struktur tabel surat_pengajuans di project ini memakai kolom:
        // id, jenis_surat, kd_jenis_surat, no_urut_surat, status, file, dst.
        // Jangan filter berdasarkan URL {jenis}, karena QR lama/baru kadang hanya membawa label URL.
        // Cukup cari berdasarkan ID surat_pengajuans agar /verify/suket/31 bisa membaca arsip yang benar.
        $surat = SuratPengajuan::with(['kelurahan.kecamatan', 'penduduk'])
            ->where('id', $id)
            ->first();

        $assets = $this->verifyAssets();

        if (! $surat) {
            return response()->view('verify.surat', [
                'found' => false,
                'assets' => $assets,
                'title' => 'Verifikasi Surat Tidak Ditemukan',
            ], 404);
        }

        $variable = $this->decodeValue($surat->variable);
        $resident = $surat->penduduk ?: Resident::where('nik', $surat->nik)->first();
        $residentData = $this->decodeValue(optional($resident)->data);
        $skpd = $surat->kelurahan ?: Skpd::with('kecamatan')->find((int) $surat->id_kel);
        $kecamatanName = $this->resolveSkpdKecamatanName($skpd, $residentData);

        $lurah = $this->resolveLurahForKelurahanId((int) $surat->id_kel);
        $camat = $this->resolveCamatForKelurahanId((int) $surat->id_kel);

        $jenisSuratDb = strtolower(trim((string) ($surat->jenis_surat ?? $jenis)));
        $jenisFinal = $jenisSuratDb !== '' ? $jenisSuratDb : $jenis;
        $isSktm = $jenisFinal === 'sktm' || $jenis === 'sktm';
        $isManual = ! empty($variable['manual_signature']) || (($variable['signature_mode'] ?? null) === 'manual');
        $hasManualProof = ! empty($variable['bukti_ttd_basah']);

        // Non-SKTM final di Lurah: status 4. SKTM final di Camat: status 9.
        // Status 5 tetap dianggap valid karena biasanya surat sudah selesai lalu diberi penilaian.
        $isFinal = $isManual
            ? $hasManualProof
            : ($isSktm
                ? in_array((int) $surat->status, [9, 5], true)
                : in_array((int) $surat->status, [4, 5], true));

				$signers = [];
				
				if ($lurah) {
					$namaLurah = $lurah->nama ?? '-';
				
					$signers[] = [
						'nama' => $namaLurah,
						'name' => $namaLurah,
						'nip' => $this->maskNip($lurah->nip ?? '-'),
						'jabatan' => $this->pejabatJabatanName($lurah, 'LURAH'),
						'sebagai' => 'Penandatangan Tingkat Kelurahan',
						'level' => 'Penandatangan Tingkat Kelurahan',
						'wilayah' => trim('Kelurahan ' . strtoupper((string) optional($skpd)->nama)),
					];
				}
				
				if ($isSktm && $camat) {
					$namaCamat = $camat->nama ?? '-';
				
					$signers[] = [
						'nama' => $namaCamat,
						'name' => $namaCamat,
						'nip' => $this->maskNip($camat->nip ?? '-'),
						'jabatan' => $this->pejabatJabatanName($camat, 'CAMAT'),
						'sebagai' => 'Penandatangan Tingkat Kecamatan',
						'level' => 'Penandatangan Tingkat Kecamatan',
						'wilayah' => trim('Kecamatan ' . $kecamatanName),
					];
				}

        if ($isManual && empty($signers)) {
            $signers[] = [
                'nama' => 'TTD Basah / Manual',
                'nip' => '-',
                'jabatan' => 'Pejabat Berwenang',
                'sebagai' => 'Verifikasi Manual',
                'wilayah' => trim('Pemerintah Kota Kediri'),
            ];
        }

        $signedAt = $this->resolveSignedAt($surat, $isSktm, $isManual);
        $keperluan = $this->resolveKeperluan($surat, $variable);
        $keperluanLainnya = $this->resolveKeperluanLainnya($surat, $variable);

        return view('verify.surat', [
            'found' => true,
            'title' => 'Verifikasi Dokumen E-Suket Kota Kediri',
            'assets' => $assets,
            'surat' => $surat,
            'variable' => $variable,
            'residentData' => $residentData,
            'skpd' => $skpd,
            'kecamatanName' => $kecamatanName,
            'nomorSurat' => $this->getNoSrt($surat),
            'tanggalSurat' => $this->formatTanggal($surat->tgl_surat),
            'signedAt' => $signedAt,
            'jenisLabel' => $this->jenisLabel($jenisFinal),
            'namaPemohon' => $this->maskName($residentData['name'] ?? $residentData['nama'] ?? $surat->kepada ?? '-'),
            // Data berikut sengaja tidak dikirim ke view publik demi perlindungan data pribadi:
            // NIK Pemohon, Keperluan Surat, Keterangan Keperluan Lainnya, dan URL PDF langsung.
            'privacyMode' => true,
            'statusName' => $this->statusName($surat, $isManual, $hasManualProof),
            'isFinal' => $isFinal,
            'isManual' => $isManual,
            'isSktm' => $isSktm,
            'signers' => $signers,
                    ]);
    }

    protected function decodeValue($value): array
    {
        if (function_exists('decode_json_data')) {
            return decode_json_data($value);
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            $decoded = json_decode(json_encode($value), true);
            return is_array($decoded) ? $decoded : [];
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function verifyAssets(): array
    {
        return [
            'logo' => $this->assetFirst([
                'img/Logo Kota Kediri.png',
                'img/Logo_Kota_Kediri.png',
                'img/Logo_Kota_Kediri_-_Seal_of_Kediri_City.svg.png',
                'img/logo.png',
                'assets/logo.png',
            ]),
            'asn_perempuan' => $this->assetFirst([
                'img/ASN Perempuan.png',
                'img/ASN_Perempuan.png',
                'img/asn-perempuan.png',
            ]),
            'asn_laki' => $this->assetFirst([
                'img/ASN Laki-Laki.png',
                'img/ASN_Laki-Laki.png',
                'img/asn-laki-laki.png',
            ]),
        ];
    }

    protected function assetFirst(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (File::exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    protected function findPejabatBySkpdAndJabatan($idSkpd, int $idJabatan): ?Pejabat
    {
        $idSkpd = (int) $idSkpd;
        if ($idSkpd <= 0) {
            return null;
        }

        return Pejabat::with(['jabatan', 'pangkat', 'skpd.kecamatan'])
            ->where('id_skpd', $idSkpd)
            ->where('id_jabatan', $idJabatan)
            ->latest('id')
            ->first();
    }

    protected function resolveLurahForKelurahanId($idKel): ?Pejabat
    {
        return $this->findPejabatBySkpdAndJabatan($idKel, 1);
    }

    protected function resolveCamatForKelurahanId($idKel): ?Pejabat
    {
        $kelurahanSkpd = Skpd::with('kecamatan')->find((int) $idKel);
        if (! $kelurahanSkpd) {
            return null;
        }

        $idKec = trim((string) $kelurahanSkpd->id_kec);
        $kecamatanSkpd = $idKec !== '' ? Skpd::where('id_region', $idKec)->first() : null;

        if (! $kecamatanSkpd && optional($kelurahanSkpd->kecamatan)->nama) {
            $kecamatanSkpd = Skpd::whereIn('nama', [
                strtoupper((string) optional($kelurahanSkpd->kecamatan)->nama),
                optional($kelurahanSkpd->kecamatan)->nama,
            ])->first();
        }

        if (! $kecamatanSkpd) {
            return $this->resolveCamatByDistrictName(optional($kelurahanSkpd->kecamatan)->nama);
        }

        return $this->findPejabatBySkpdAndJabatan($kecamatanSkpd->id, 2);
    }

    protected function resolveCamatByDistrictName(?string $districtName): ?Pejabat
    {
        $districtName = strtoupper(trim((string) $districtName));

        $map = [
            'MOJOROTO' => 64,
            'KOTA' => 65,
            'PESANTREN' => 66,
        ];

        $idSkpd = $map[$districtName] ?? null;
        return $idSkpd ? $this->findPejabatBySkpdAndJabatan($idSkpd, 2) : null;
    }

    protected function resolveSkpdKecamatanName($skpd, array $residentData = []): string
    {
        $name = optional(optional($skpd)->kecamatan)->nama;

        if (! $name && ! empty($skpd->id_kec)) {
            $kecamatanSkpd = Skpd::where('id_region', trim((string) $skpd->id_kec))->first();
            $name = optional($kecamatanSkpd)->nama;
        }

        if (! $name && ! empty($residentData['kecamatan_nm'])) {
            $name = $residentData['kecamatan_nm'];
        }

        return strtoupper(trim((string) $name));
    }

    protected function pejabatJabatanName($pejabat, string $fallback): string
    {
        $name = trim((string) optional(optional($pejabat)->jabatan)->nama);
        return $name !== '' ? strtoupper($name) : strtoupper($fallback);
    }


    protected function maskName($name): string
    {
        $name = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $name)));

        if ($name === '' || $name === '-') {
            return '-';
        }

        $words = preg_split('/\s+/', $name) ?: [];
        $masked = [];

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') {
                continue;
            }

            $length = strlen($word);

            if ($length <= 1) {
                $masked[] = 'X';
                continue;
            }

            if ($length === 2) {
                $masked[] = substr($word, 0, 1) . 'X';
                continue;
            }

            $masked[] = substr($word, 0, 2) . str_repeat('X', max(3, $length - 2));
        }

        return implode(' ', $masked) ?: '-';
    }

    protected function maskNip($nip): string
    {
        $nip = trim((string) $nip);

        if ($nip === '' || $nip === '-') {
            return '-';
        }

        $digits = preg_replace('/\D+/', '', $nip);
        $length = strlen($digits ?: $nip);

        return str_repeat('X', max(12, $length));
    }

    protected function resolveSignedAt(SuratPengajuan $surat, bool $isSktm, bool $isManual): ?string
    {
        if ($isManual) {
            return $this->formatTanggalJam($surat->updated_at);
        }

        $targetStatuses = $isSktm ? [9, 5] : [4, 5];

        $log = Log_surat::query()
            ->where('tabel_surat', 'surat_pengajuans')
            ->where('id_surat', $surat->id)
            ->whereIn('status_surat', $targetStatuses)
            ->latest('created_at')
            ->first();

        return $this->formatTanggalJam(optional($log)->created_at ?: $surat->updated_at);
    }

    protected function formatTanggal($value): string
    {
        if (! $value) {
            return '-';
        }

        try {
            return Carbon::parse($value)->locale('id')->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function formatTanggalJam($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function jenisLabel(string $jenis): string
    {
        return [
            'skbn' => 'Surat Keterangan Belum Nikah',
            'sktm' => 'Surat Keterangan Tidak Mampu',
            'skdom' => 'Surat Keterangan Domisili',
            'skusaha' => 'Surat Keterangan Usaha',
            'skhsl' => 'Surat Keterangan Penghasilan',
            'skboro' => 'Surat Keterangan Boro',
            'skkelahiran' => 'Surat Keterangan Kelahiran',
            'skkematian' => 'Surat Keterangan Kematian',
            'suket' => 'Surat Keterangan',
        ][$jenis] ?? strtoupper($jenis);
    }

    protected function resolveKeperluan(SuratPengajuan $surat, array $variable): string
    {
        $peruntukan = trim((string) ($surat->peruntukan ?? ''));
        $lainnya = $this->resolveKeperluanLainnya($surat, $variable);

        if (Str::lower($peruntukan) === 'lainnya' && $lainnya !== '-') {
            return 'Lainnya';
        }

        if (! empty($variable['surat_keperluan'])) {
            return (string) $variable['surat_keperluan'];
        }

        if (! empty($variable['keperluan'])) {
            return (string) $variable['keperluan'];
        }

        if ($peruntukan !== '') {
            return Str::title($peruntukan);
        }

        return '-';
    }

    protected function resolveKeperluanLainnya(SuratPengajuan $surat, array $variable): string
    {
        $peruntukan = Str::lower(trim((string) ($surat->peruntukan ?? '')));

        $candidates = [
            $variable['keperluan_lainnya'] ?? null,
            $variable['keterangan_lainnya'] ?? null,
            $variable['keterangan_tambahan'] ?? null,
            $variable['surat_catatan'] ?? null,
            $variable['catatan'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value !== '' && $value !== '-') {
                return $value;
            }
        }

        return $peruntukan === 'lainnya' ? 'Belum ada keterangan lainnya.' : '-';
    }

    protected function statusName(SuratPengajuan $surat, bool $isManual, bool $hasManualProof): string
    {
        if ($isManual) {
            return $hasManualProof ? 'Terverifikasi TTD Basah' : 'TTD Basah Belum Lengkap';
        }

        return match ((int) $surat->status) {
            4 => strtolower((string) $surat->jenis_surat) === 'sktm' ? 'TTE Lurah Selesai - Menunggu Camat' : 'Terverifikasi TTE Lurah',
            5 => 'Terverifikasi dan Telah Dinilai',
            9 => 'Terverifikasi TTE Camat',
            11 => 'Menunggu Verifikasi Sekcam',
            8 => 'Menunggu TTE Camat',
            default => 'Belum Final',
        };
    }
}

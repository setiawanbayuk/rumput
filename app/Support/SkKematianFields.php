<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Bidang SK Kematian (F-2.29). Tidak mengubah alur surat lain / TTE.
 * Identitas jenazah memakai residents.data, sehingga OCR tetap satu sumber.
 */
final class SkKematianFields
{
    public const GROUPS = [
        'Kepala Keluarga' => [
            'nama_kk' => 'Nama Kepala Keluarga',
            'nomor_kk' => 'Nomor Kartu Keluarga',
        ],
        'Keterangan Kematian' => [
            'jenazah_umur' => 'Umur Jenazah (tahun)',
            'jenazah_anak_ke' => 'Anak Ke-',
            'tanggal_meninggal' => 'Tanggal Kematian',
            'jam_meninggal' => 'Pukul Kematian',
            'penyebab_meninggal' => 'Sebab Kematian',
            'tempat_meninggal' => 'Tempat Kematian',
            'yang_menerangkan' => 'Yang Menerangkan',
        ],
        'Ayah' => [
            'ayah_nik' => 'NIK Ayah',
            'ayah_nama' => 'Nama Ayah',
            'ayah_tgl_lahir' => 'Tanggal Lahir Ayah',
            'ayah_umur' => 'Umur Ayah (tahun)',
            'ayah_pekerjaan' => 'Pekerjaan Ayah',
            'ayah_alamat' => 'Alamat Ayah',
            'ayah_kelurahan' => 'Desa/Kelurahan Ayah',
            'ayah_kecamatan' => 'Kecamatan Ayah',
            'ayah_kabko' => 'Kabupaten/Kota Ayah',
            'ayah_provinsi' => 'Provinsi Ayah',
        ],
        'Ibu' => [
            'ibu_nik' => 'NIK Ibu',
            'ibu_nama' => 'Nama Ibu',
            'ibu_tgl_lahir' => 'Tanggal Lahir Ibu',
            'ibu_umur' => 'Umur Ibu (tahun)',
            'ibu_pekerjaan' => 'Pekerjaan Ibu',
            'ibu_alamat' => 'Alamat Ibu',
            'ibu_kelurahan' => 'Desa/Kelurahan Ibu',
            'ibu_kecamatan' => 'Kecamatan Ibu',
            'ibu_kabko' => 'Kabupaten/Kota Ibu',
            'ibu_provinsi' => 'Provinsi Ibu',
        ],
        'Pelapor' => [
            'pelapor_nik' => 'NIK Pelapor',
            'pelapor_nama' => 'Nama Pelapor',
            'pelapor_tgl_lahir' => 'Tanggal Lahir Pelapor',
            'pelapor_umur' => 'Umur Pelapor (tahun)',
            'pelapor_pekerjaan' => 'Pekerjaan Pelapor',
            'pelapor_alamat' => 'Alamat Pelapor',
            'pelapor_kelurahan' => 'Desa/Kelurahan Pelapor',
            'pelapor_kecamatan' => 'Kecamatan Pelapor',
            'pelapor_kabko' => 'Kabupaten/Kota Pelapor',
            'pelapor_provinsi' => 'Provinsi Pelapor',
        ],
        'Saksi 1' => [
            'saksi1_nik' => 'NIK Saksi 1',
            'saksi1_nama' => 'Nama Saksi 1',
            'saksi1_tgl_lahir' => 'Tanggal Lahir Saksi 1',
            'saksi1_umur' => 'Umur Saksi 1 (tahun)',
            'saksi1_pekerjaan' => 'Pekerjaan Saksi 1',
            'saksi1_alamat' => 'Alamat Saksi 1',
            'saksi1_kelurahan' => 'Desa/Kelurahan Saksi 1',
            'saksi1_kecamatan' => 'Kecamatan Saksi 1',
            'saksi1_kabko' => 'Kabupaten/Kota Saksi 1',
            'saksi1_provinsi' => 'Provinsi Saksi 1',
        ],
        'Saksi 2' => [
            'saksi2_nik' => 'NIK Saksi 2',
            'saksi2_nama' => 'Nama Saksi 2',
            'saksi2_tgl_lahir' => 'Tanggal Lahir Saksi 2',
            'saksi2_umur' => 'Umur Saksi 2 (tahun)',
            'saksi2_pekerjaan' => 'Pekerjaan Saksi 2',
            'saksi2_alamat' => 'Alamat Saksi 2',
            'saksi2_kelurahan' => 'Desa/Kelurahan Saksi 2',
            'saksi2_kecamatan' => 'Kecamatan Saksi 2',
            'saksi2_kabko' => 'Kabupaten/Kota Saksi 2',
            'saksi2_provinsi' => 'Provinsi Saksi 2',
        ],
    ];

    /**
     * Kolom yang benar-benar wajib untuk SK Kematian.
     * Hanya data Ayah/Ibu yang boleh kosong; NIK Pelapor dan kedua Saksi tetap wajib.
     */
    public const REQUIRED = [
        'nama_kk', 'nomor_kk', 'tanggal_meninggal', 'jam_meninggal',
        'penyebab_meninggal', 'tempat_meninggal', 'yang_menerangkan',
        'pelapor_nik', 'pelapor_nama', 'saksi1_nik', 'saksi1_nama',
        'saksi2_nik', 'saksi2_nama',
    ];

    public static function rules(): array
    {
        $dates = ['tanggal_meninggal'];
        $numerics = ['nomor_kk','ayah_nik','ibu_nik','pelapor_nik','saksi1_nik','saksi2_nik'];
        $rules = [];
        foreach (self::GROUPS as $group) {
            foreach ($group as $field => $label) {
                if (in_array($field, $dates, true)) {
                    $rules[$field] = 'required|date|before_or_equal:today';
                } elseif (in_array($field, $numerics, true)) {
                    $rules[$field] = (in_array($field, self::REQUIRED, true) ? 'required' : 'nullable') . '|digits:16';
                } elseif (in_array($field, ['ayah_tgl_lahir','ibu_tgl_lahir','pelapor_tgl_lahir','saksi1_tgl_lahir','saksi2_tgl_lahir'], true)) {
                    $rules[$field] = 'nullable|date|before_or_equal:today';
                } elseif ($field === 'jam_meninggal') {
                    $rules[$field] = 'required|date_format:H:i';
                } elseif (in_array($field, ['jenazah_umur','jenazah_anak_ke','ayah_umur','ibu_umur','pelapor_umur','saksi1_umur','saksi2_umur'], true)) {
                    $rules[$field] = 'nullable|integer|min:0|max:150';
                } else {
                    $rules[$field] = (in_array($field, self::REQUIRED, true) ? 'required' : 'nullable') . '|string|max:500';
                }
            }
        }
        return $rules;
    }

    private static function displayDate($value): string
    {
        if (!$value) return '-';
        try { return Carbon::parse($value)->format('d-m-Y'); }
        catch (\Throwable $e) { return (string) $value; }
    }

    public static function pdfData(array $data, array $variable): array
    {
        // Semua field sederhana menjadi placeholder Word; field tanpa nilai ditampilkan '-'.
        foreach (self::GROUPS as $group) {
            foreach ($group as $key => $label) {
                $value = $variable[$key] ?? '';
                if (str_ends_with($key, '_tgl_lahir') || $key === 'tanggal_meninggal') {
                    $value = self::displayDate($value);
                }
                $data[$key] = trim((string) $value) !== '' ? (string) $value : '-';
            }
        }
        $data['jenazah_nik'] = (string) ($data['surat_nik'] ?? '-');
        $data['jenazah_nama'] = (string) ($data['surat_nama'] ?? '-');
        $data['jenazah_gender'] = (string) ($data['surat_gender'] ?? '-');
        $data['jenazah_tanggal_lahir'] = (string) ($data['surat_tgll'] ?? '-');
        $data['jenazah_tempat_lahir'] = (string) ($data['surat_tmpl'] ?? '-');
        $data['jenazah_agama'] = (string) ($data['surat_agama'] ?? '-');
        $data['jenazah_pekerjaan'] = (string) ($data['surat_pekerjaan'] ?? '-');
        $data['jenazah_alamat'] = (string) ($data['surat_alamat'] ?? '-');
        $data['jenazah_kelurahan'] = (string) ($data['kelurahan_nm'] ?? '-');
        $data['jenazah_kecamatan'] = (string) ($data['kecamatan_nm'] ?? '-');
        $data['jenazah_kabko'] = (string) ($data['kabko_nm'] ?? '-');
        $data['jenazah_provinsi'] = (string) ($data['provinsi_nm'] ?? '-');
        // Bila foto belum diperbarui, data lama Kematian masih boleh dibaca.
        $data['jenazah_tanggal_meninggal'] = $data['tanggal_meninggal'] !== '-'
            ? $data['tanggal_meninggal'] : self::displayDate($variable['tanggal_meninggal'] ?? null);
        return $data;
    }
}

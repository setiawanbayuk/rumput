<?php

namespace App\Traits;

trait StatusSuratTrait
{
    protected function suratVariableArray(): array
    {
        $value = $this->variable ?? [];
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            $unserialized = decode_json_data($value);
            if ($unserialized !== false && is_array($unserialized)) {
                return $unserialized;
            }
        }
        return [];
    }

    protected function detectSubmitterType(): string
    {
        $variable = $this->suratVariableArray();
        $submitter = strtolower(trim((string) ($variable['submitter_type'] ?? '')));

        if (in_array($submitter, ['warga', 'admin'], true)) {
            return $submitter;
        }

        $firstLog = \App\Models\Log_surat::query()
            ->where('tabel_surat', 'surat_pengajuans')
            ->where('id_surat', $this->id)
            ->orderBy('id', 'asc')
            ->first();

        if ($firstLog) {
            $firstStatus = (int) $firstLog->status_surat;

            if ($firstStatus === 0) {
                return 'warga';
            }

            if ($firstStatus === 1) {
                return 'admin';
            }
        }

        return ((int) $this->status === 0) ? 'warga' : 'admin';
    }

    public function getStAttribute()
    {
        $variable = $this->suratVariableArray();
        $submitter = $this->detectSubmitterType();
        $isWarga = $submitter === 'warga';
        $isManual = !empty($variable['manual_signature']) || (($variable['signature_mode'] ?? null) === 'manual');
        $hasProof = !empty($variable['bukti_ttd_basah']);

        if ($isManual) {
            return [
                'name' => $hasProof ? 'Sudah Upload Bukti' : 'TTD Basah - Belum Upload Bukti',
                'color' => $hasProof ? 'green' : 'purple',
            ];
        }

        return match ((int) $this->status) {
            0 => ['name' => 'Warga', 'color' => '#6B7280'],
            1 => ['name' => $isWarga ? 'Warga Mandiri' : 'Admin Kelurahan', 'color' => 'blue'],
            2 => ['name' => $isWarga ? 'Warga - Sekkel' : 'Admin - Sekkel', 'color' => 'orange'],
            3 => ['name' => $isWarga ? 'Warga - Lurah' : 'Admin - Lurah', 'color' => 'orange'],
            4 => ['name' => $isWarga ? 'Disetujui Warga' : 'Disetujui Lurah', 'color' => 'green'],
            5 => ['name' => 'Dinilai', 'color' => '#EFBF04'],
            6 => ['name' => $isWarga ? 'Ditolak Warga' : 'Ditolak Lurah', 'color' => 'red'],
            7 => ['name' => 'Dihapus', 'color' => 'red'],
            8 => ['name' => $isWarga ? 'Warga - Camat' : 'Admin - Camat', 'color' => '#B2784A'],
            9 => ['name' => $isWarga ? 'Disetujui Camat Warga' : 'Disetujui Admin', 'color' => '#A78BFA'],
            default => ['name' => 'Pengajuan', 'color' => 'black'],
        };
    }

    public function getStatusName()
    {
        return $this->st['name'];
    }
}

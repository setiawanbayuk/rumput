@php
    $mask = function ($value) {
        $digits = preg_replace('/\D/', '', (string) $value);
        return $digits === '' ? '' : str_repeat('*', strlen($digits));
    };
    $readData = function ($row) {
        $resident = \App\Models\Resident::where('nik', $row->nik)->first();
        $residentData = $resident ? decode_json_data($resident->data) : [];
        $variable = is_array($row->variable) ? $row->variable : decode_json_data($row->variable);
        if (!is_array($variable)) { $variable = []; }
        return [$resident, $residentData, $variable];
    };
    $statusText = function ($row) {
        $name = strtolower((string) data_get($row->st ?? [], 'name'));
        if ($name !== '') { return data_get($row->st, 'name'); }
        return match ((int) $row->status) {
            4 => 'Disetujui Lurah',
            9 => 'Disetujui Camat',
            default => 'Sudah Upload Bukti',
        };
    };
@endphp
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="15" style="font-weight:bold;font-size:16px;text-align:center;">REKAP {{ strtoupper($label) }}</th>
        </tr>
        <tr>
            <th colspan="15" style="font-weight:bold;">Wilayah: {{ $scope['unit_name'] ?? '-' }}</th>
        </tr>
        @if ($jenis === 'skbn')
            <tr>
                <th colspan="15" style="font-weight:bold;">Filter SKBN: {{ $filterSkbn === 'menikah' ? 'Menikah' : 'Keperluan Lainnya' }}</th>
            </tr>
        @endif
        <tr>
            <th>No</th>
            <th>Tanggal Surat</th>
            <th>No. Surat</th>
            <th>Status</th>
            <th>NIK</th>
            <th>No. KK</th>
            <th>Nama</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Alamat</th>
            <th>RW</th>
            <th>RT</th>
            <th>Kelurahan</th>
            <th>Keperluan/Keterangan</th>
        </tr>
        @forelse ($rows as $row)
            @php
                [$resident, $penduduk, $variable] = $readData($row);
                $kk = $penduduk['kk'] ?? ($resident->kk ?? ($variable['kk'] ?? ''));
                $skpd = \App\Models\Skpd::with('kelurahan')->find($row->id_kel ?? null);
                try { $tanggalObj = $row->tgl_surat ? \Carbon\Carbon::parse($row->tgl_surat) : ($row->created_at ? \Carbon\Carbon::parse($row->created_at) : null); } catch (\Throwable $e) { $tanggalObj = null; }
                $nomorSurat = trim(($row->kd_jenis_surat ?? '') . '/' . ($row->no_urut_surat ?? '') . '/' . ($skpd->instansi_kode ?? '') . '/' . ($tanggalObj ? $tanggalObj->format('Y') : date('Y')), '/');
                if (!empty($row->nomor_surat)) { $nomorSurat = $row->nomor_surat; }
                $keperluan = $row->peruntukan ?? ($variable['keperluan_lainnya'] ?? $variable['surat_keperluan'] ?? $variable['keperluan'] ?? $row->kepada ?? '');
                $tanggalText = $tanggalObj ? $tanggalObj->format('d-m-Y') : '';
                $nama = $penduduk['name'] ?? $variable['name'] ?? $row->kepada ?? '-';
                $tempat = $penduduk['tempat_lhr'] ?? $variable['tempat_lhr'] ?? '-';
                $tglLahir = $penduduk['tgl_lhr'] ?? $variable['tgl_lhr'] ?? '-';
                $gender = $penduduk['gender_nm'] ?? $variable['gender_nm'] ?? '-';
                $alamat = $penduduk['alamat'] ?? $variable['alamat'] ?? '-';
                $rw = $penduduk['rw'] ?? $variable['rw'] ?? $row->id_rw ?? '-';
                $rt = $penduduk['rt'] ?? $variable['rt'] ?? $row->id_rt ?? '-';
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $tanggalText }}</td>
                <td>{{ $nomorSurat }}</td>
                <td>{{ $statusText($row) }}</td>
                <td style="mso-number-format:'\@';">{{ $mask($row->nik) }}</td>
                <td style="mso-number-format:'\@';">{{ $mask($kk) }}</td>
                <td>{{ $nama }}</td>
                <td>{{ $tempat }}</td>
                <td>{{ $tglLahir }}</td>
                <td>{{ $gender }}</td>
                <td>{{ $alamat }}</td>
                <td>{{ $rw }}</td>
                <td>{{ $rt }}</td>
                <td>{{ optional($skpd->kelurahan ?? null)->nama ?? ($skpd->nama ?? '-') }}</td>
                <td>{{ $keperluan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="15">Data tidak ditemukan. Pastikan surat sudah final: Disetujui Lurah, Disetujui Camat, atau TTD Basah sudah upload bukti.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>

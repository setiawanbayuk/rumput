@extends('layouts.main')

@section('title', $title ?? 'Preview Surat')

@section('content')
@php
    $residentData = $residentData ?? [];
    $variableData = $variableData ?? [];
    $surat = $surat ?? null;
    $nomorSurat = $nomorSurat ?? ($surat ? ($surat->kd_jenis_surat ?? '').'/'.($surat->no_urut_surat ?? '') : '-');

    function preview_value($value) {
        if (is_array($value)) {
            return implode(', ', array_filter($value, fn($v) => $v !== null && $v !== ''));
        }
        return $value ?? '-';
    }
@endphp

<div class="container mt-3 mb-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header border-0 py-3" style="background:#AEA07A; border-radius:1rem 1rem 0 0;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h4 class="mb-1 text-white fw-bold">Preview Surat {{ strtoupper($surat->jenis_surat ?? '') }}</h4>
                    <div class="text-white-50">Halaman preview data sebelum PDF/cetak</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.surat.index') }}" class="btn btn-light btn-sm">
                        Kembali
                    </a>
                    @if($surat)
                        <a href="{{ route('admin.surat.cetak', $surat->id) }}" class="btn btn-dark btn-sm">
                            Cetak PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(!$surat)
                <div class="alert alert-danger mb-0">Data surat tidak ditemukan.</div>
            @else
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Informasi Surat</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <tr>
                                            <th width="38%">Jenis Surat</th>
                                            <td>{{ strtoupper($surat->jenis_surat ?? '-') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kode Jenis Surat</th>
                                            <td>{{ $surat->kd_jenis_surat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. Urut</th>
                                            <td>{{ $surat->no_urut_surat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Surat</th>
                                            <td>{{ $nomorSurat }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Surat</th>
                                            <td>{{ !empty($surat->tgl_surat) ? \Carbon\Carbon::parse($surat->tgl_surat)->translatedFormat('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>{{ $surat->nik ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kepada</th>
                                            <td>{{ $surat->kepada ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Peruntukan</th>
                                            <td>{{ $surat->peruntukan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ $surat->status ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pengantar</th>
                                            <td>
                                                @if(!empty($surat->pengantar))
                                                    <a href="{{ asset($surat->pengantar) }}" target="_blank">Lihat File Pengantar</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card h-100 border rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Data Penduduk / Pemohon</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <tr><th width="38%">No. KK</th><td>{{ $resident->kk ?? ($residentData['kk'] ?? '-') }}</td></tr>
                                        <tr><th>Nama</th><td>{{ $residentData['name'] ?? '-' }}</td></tr>
                                        <tr><th>Jenis Kelamin</th><td>{{ $residentData['gender_nm'] ?? ($residentData['gender'] ?? '-') }}</td></tr>
                                        <tr><th>Status Perkawinan</th><td>{{ $residentData['status_kwn_nm'] ?? ($residentData['status_kwn'] ?? '-') }}</td></tr>
                                        <tr><th>Kewarganegaraan</th><td>{{ $residentData['kewarganegaraan_nm'] ?? ($residentData['kewarganegaraan'] ?? '-') }}</td></tr>
                                        <tr><th>Tempat Lahir</th><td>{{ $residentData['tempat_lhr'] ?? '-' }}</td></tr>
                                        <tr><th>Tanggal Lahir</th><td>{{ $residentData['tgl_lhr'] ?? '-' }}</td></tr>
                                        <tr><th>Agama</th><td>{{ $residentData['agama_nm'] ?? ($residentData['agama'] ?? '-') }}</td></tr>
                                        <tr><th>Pendidikan</th><td>{{ $residentData['pendidikan_nm'] ?? ($residentData['pendidikan'] ?? '-') }}</td></tr>
                                        <tr><th>Pekerjaan</th><td>{{ $residentData['pekerjaan_nm'] ?? ($residentData['pekerjaan'] ?? '-') }}</td></tr>
                                        <tr><th>Provinsi</th><td>{{ $residentData['provinsi_nm'] ?? ($residentData['provinsi'] ?? '-') }}</td></tr>
                                        <tr><th>Kab/Kota</th><td>{{ $residentData['kabko_nm'] ?? ($residentData['kabko'] ?? '-') }}</td></tr>
                                        <tr><th>Kecamatan</th><td>{{ $residentData['kecamatan_nm'] ?? ($residentData['kecamatan'] ?? '-') }}</td></tr>
                                        <tr><th>Kelurahan</th><td>{{ $residentData['kelurahan_nm'] ?? ($residentData['kelurahan'] ?? '-') }}</td></tr>
                                        <tr><th>RW / RT</th><td>{{ ($residentData['rw_nm'] ?? $residentData['rw'] ?? '-') . ' / ' . ($residentData['rt_nm'] ?? $residentData['rt'] ?? '-') }}</td></tr>
                                        <tr><th>Alamat</th><td>{{ $residentData['alamat'] ?? '-' }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Variabel Tambahan Surat</h5>

                                @if(empty($variableData))
                                    <div class="alert alert-secondary mb-0">Tidak ada variabel tambahan.</div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="30%">Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($variableData as $key => $value)
                                                    <tr>
                                                        <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                        <td>{{ preview_value($value) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Ringkasan Siap Cetak</h5>
                                <div class="p-3 rounded-3 bg-light border">
                                    <p class="mb-2"><strong>Nomor Surat:</strong> {{ $nomorSurat }}</p>
                                    <p class="mb-2"><strong>Nama:</strong> {{ $residentData['name'] ?? '-' }}</p>
                                    <p class="mb-2"><strong>NIK:</strong> {{ $surat->nik ?? '-' }}</p>
                                    <p class="mb-2"><strong>Alamat:</strong> {{ $residentData['alamat'] ?? '-' }}</p>
                                    <p class="mb-2"><strong>Peruntukan:</strong> {{ $surat->peruntukan ?? '-' }}</p>
                                    <p class="mb-0"><strong>Kepada:</strong> {{ $surat->kepada ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

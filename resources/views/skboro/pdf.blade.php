<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
    <style>
        .text-center {
            text-align: center;
        }

        .font-arial {
            font-family: Arial, Helvetica, sans-serif;
        }

        .kop {
            margin-top: -30px;
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
        }

        .divA {
            height: 1px;
            border-bottom: 2px solid black;
        }

        .divB {
            height: 1px;
            border-bottom: 1px solid black;
        }

        .isi {
            width: 100%;
            margin-left: 60px;
            text-align: justify;
        }

        .num {
            margin-left: 40px;
        }

        .kiri {
            width: 270px;
        }

        .titik {
            width: 1px;
        }

        td {
            vertical-align: top;
        }

        .footer {
            width: 100%;
            /* height: 150px; */
            position: fixed;
            bottom: 0px;
            left: 0px;
            margin-bottom: -30px;
        }

        /* table,
        th,
        td {
            border: 1px solid black;
        } */
    </style>
</head>

<body class="font-arial">

    <div class="kop">
        <table style="width: 100%;">
            <tr>
                <td>
                    <img class="logo" src="img/logo.png" alt="">
                </td>
                <td>
                    <div class="text-center">
                        <div style="font-size: 16">PEMERINTAH KOTA KEDIRI</div>
                        <div style="font-size: 16">KECAMATAN {{ strtoupper($pejabat->skpd->kecamatan->nama) }}</div>
                        <div style="font-size: 20">KELURAHAN {{ strtoupper($pejabat->skpd->nama) }}</div>
                        <div>{{ $pejabat->skpd->instansi_alamat }} Telp. {{ $pejabat->skpd->instansi_telp }} Kode Pos
                            {{ $pejabat->skpd->instansi_kode_pos }} </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="divA"></div>
        <div class="divB"></div>
    </div>
    <div class="text-center" style="text-decoration: underline;font-weight: bold;">SURAT BEPERGIAN SEMENTARA/BORO</div>
    <div class="text-center">Nomor : {{ $nomorSurat }}</div>
    <br>
    <div class="num">Yang bertanda tangan dibawah ini Lurah {{ ucfirst(strtolower($pejabat->skpd->nama)) }} Kecamatan
        {{ ucfirst(strtolower($pejabat->skpd->kecamatan->nama)) }} Kota Kediri menerangkan bahwa:</div>
    <br>
    <div>
        <table class="isi" style="width: 675px;">
            <tr>
                <td class="titik">a.</td>
                <td class="kiri">Nama Lengkap</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['name'] }}</td>
            </tr>
            <tr>
                <td class="titik">b.</td>
                <td class="kiri">NIK</td>
                <td class="titik">:</td>
                <td>{{ $surat->nik }}</td>
            </tr>
            <tr>
                <td class="titik">c.</td>
                <td class="kiri">Tempat / Tanggal Lahir</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['tempat_lhr'] }}, {{ strtoupper($penduduk['tgl_lhr']) }} </td>
            </tr>
            <tr>
                <td class="titik">d.</td>
                <td class="kiri">Jenis Kelamin</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['gender_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">e.</td>
                <td class="kiri">Status Perkawinan</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['status_kwn_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">f.</td>
                <td class="kiri">Agama</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['agama_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">g.</td>
                <td class="kiri">Pekerjaan</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['pekerjaan_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">h.</td>
                <td class="kiri">Pendidikan Terakhir</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['pendidikan_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">i.</td>
                <td class="kiri">Alamat Tempat Tinggal</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['alamat'] }}</td>
            </tr>
            <tr>
                <td class="titik"></td>
                <td class="kiri"></td>
                <td class="titik"></td>
                <td>
                    KEL. {{ $penduduk['kelurahan_nm'] }} KEC.
                    {{ $penduduk['kecamatan_nm'] }} {{ $penduduk['kabko_nm'] }}</td>
            </tr>
            <tr>
                <td class="titik">j.</td>
                <td class="kiri">Mulai Berlaku Tanggal</td>
                <td class="titik">:</td>
                <td>{{ $surat->tgl_awal }} s/d {{ $surat->tgl_akhir }}</td>
            </tr>
            <tr>
                <td class="titik">k.</td>
                <td class="kiri">Tempat Tujuan</td>
                <td class="titik">:</td>
                <td>Desa / Kelurahan : {{ $surat->kel_boro_nm }} Kecamatan : {{ $surat->kec_boro_nm }} Kabupaten :
                    {{ $surat->kabko_boro_nm }} Provinsi : {{ $surat->prov_boro_nm }}</td>
            </tr>
            <tr>
                <td class="titik">l.</td>
                <td class="kiri">Keperluan</td>
                <td class="titik">:</td>
                <td>{{ $surat->peruntukan }}</td>
            </tr>

            <tr>
                <td class="titik">m.</td>
                <td class="kiri">Pengikut</td>
                <td class="titik">:</td>
                <td>{{ $surat->pengikut }} orang</td>
            </tr>
        </table>
        <br>
        <table
            style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;margin-left: auto;margin-right: auto;">
            <thead>
                <tr>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">NO
                    </th>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">NAMA
                    </th>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">NIK
                    </th>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">USIA
                    </th>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                        STATUS PERKAWINAN</th>
                    <th style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                        HUBUNGAN KELUARGA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengikut as $key => $item)
                    <tr>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $key + 1 }}</td>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $item->nama }}</td>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $item->nik }}</td>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $item->umur }}</td>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $item->status_kwn_nm }}
                        </td>
                        <td style="border: 1px solid black;border-collapse: collapse;text-align:center;font-size:14px;">
                            {{ $item->hubungan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
    <div class="num">Demikian Surat berpergian sementara / boro ini dibuat untuk dipergunakan seperlunya.</div>
    <br>
    <div>
        <table class="text-center" style="width: 100%">
            <tr>
                <td></td>
                <td>Kediri, {{ $tglSurat }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Mengetahui,</td>
            </tr>
            <tr>
                <td>Pemohon</td>
                <td>{{ ucfirst($pejabat->jabatan->nama) }} {{ ucfirst(strtolower($pejabat->skpd->nama)) }}</td>
            </tr>
            <tr>
                <td></td>
                <td>
                    @if ($url != '')
                        <img src="data:image/png;base64, {!! $url !!} " style="width: 100px">
                    @else
                        <img src="img/placeholder.png" alt="" style="width: 100px">
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ $penduduk['name'] }}</td>
                <td style="text-decoration: underline;font-weight: bold;">{{ $pejabat->nama }}</td>
            </tr>
            <tr>
                <td></td>
                <td>NIP. {{ $pejabat->nip }}</td>
            </tr>
        </table>
    </div>
    <div>
        <img class="footer" src="img/catatan.png" alt="" style="width: 100%">
    </div>
</body>

</html>

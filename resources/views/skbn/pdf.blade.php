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
    <div class="text-center" style="text-decoration: underline;font-weight: bold;">SURAT KETERANGAN</div>
    <div class="text-center">Nomor : {{ $nomorSurat }}</div>
    <br>
    <div class="num">1. Yang bertanda tangan dibawah ini :</div>
    <div>
        <table class="isi" style="width: 675px;">
            <tr>
                <td class="kiri">Nama</td>
                <td class="titik">:</td>
                <td style="font-weight: bold;">{{ $pejabat->nama }}</td>
            </tr>
            <tr>
                <td class="kiri">Jabatan</td>
                <td class="titik">:</td>
                <td>{{ ucfirst($pejabat->jabatan->nama) }} {{ ucfirst(strtolower($pejabat->skpd->nama)) }}</td>
            </tr>
        </table>
    </div>
    <br>
    <div class="isi">Dengan ini menerangkan atas pernyataan yang bersangkutan bahwa :</div>
    <div>
        <table class="isi" style="width: 675px;">
            <tr>
                <td class="kiri">a. Nama Lengkap</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['name'] }}</td>
            </tr>
            <tr>
                <td class="kiri">b. NIK</td>
                <td class="titik">:</td>
                <td>{{ $surat->nik }}</td>
            </tr>
            <tr>
                <td class="kiri">c. Tempat / Tanggal Lahir</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['tempat_lhr'] }}, {{ strtoupper($penduduk['tgl_lhr']) }} </td>
            </tr>
            <tr>
                <td class="kiri">d. Jenis Kelamin</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['gender_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">e. Status Perkawinan</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['status_kwn_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">f. Agama</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['agama_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">g. Pekerjaan</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['pekerjaan_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">h. Pendidikan Terakhir</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['pendidikan_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">i. Alamat Tempat Tinggal</td>
                <td class="titik">:</td>
                <td>{{ $penduduk['alamat'] }}</td>
            </tr>
            <tr>
                <td class="kiri"></td>
                <td class="titik"></td>
                <td>
                    KEL. {{ $penduduk['kelurahan_nm'] }} KEC.
                    {{ $penduduk['kecamatan_nm'] }} {{ $penduduk['kabko_nm'] }}</td>
            </tr>
            <tr>
                <td class="kiri">j. Keterangan</td>
                <td class="titik">:</td>
                <td>Menurut pernyataan yang bersangkutan belum pernah menikah / kawin.</td>
            </tr>
        </table>
    </div>
    <br>
    <div class="num">2. Surat Keterangan Kelurahan ini diberikan :</div>
    <div>
        <table class="isi" style="width: 675px;">
            <tr>
                <td class="kiri">Kepada</td>
                <td class="titik">:</td>
                <td>{{ $surat->kepada }}</td>
            </tr>
            <tr>
                <td class="kiri">Untuk</td>
                <td class="titik">:</td>
                <td>{{ $surat->peruntukan }}</td>
            </tr>
        </table>
    </div>
    <br>
    <div class="num">3. Demikian Surat Keterangan Kelurahan ini dibuat untuk dipergunakan seperlunya.</div>
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

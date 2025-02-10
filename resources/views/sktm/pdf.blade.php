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
            /* font-size: 10; */
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
            width: 250px;
        }

        .titik {
            width: 1px;
        }

        .space {
            width: 13px;
        }

        td {
            vertical-align: top;
            padding: 0;
        }

        .footer {
            height: 50px;
            position: fixed;
            bottom: 0px;
            /* left: 0px; */
            margin-bottom: -30px;
            margin-left: -40px;
            left: 50%;
            transform: translateX(-50%);
        }

        .qr {
            position: fixed;
            bottom: 0px;
            left: 0px;
            margin-bottom: -30px;
            margin-left: -30px;
            width: 100px;
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
                        <div>{{ $pejabat->skpd->instansi_alamat }} Kode Pos
                            {{ $pejabat->skpd->instansi_kode_pos }} Telp. {{ $pejabat->skpd->instansi_telp }} </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="divA"></div>
        <div class="divB"></div>
    </div>
    <div class="text-center" style="text-decoration: underline;font-weight: bold;">SURAT KETERANGAN MISKIN</div>
    <div class="text-center">Nomor : {{ $nomorSurat }}</div>
    <br>
    <div class="num">1. Yang bertanda tangan dibawah ini :</div>
    <div>
        <table class="isi" style="width: 675px;">
            <tr>
                <td class="kiri">Nama</td>
                <td class="space"></td>
                <td class="titik">:</td>
                <td style="font-weight: bold;">{{ $pejabat->nama }}</td>
            </tr>
            <tr>
                <td class="kiri">Jabatan</td>
                <td class="space"></td>
                <td class="titik">:</td>
                <td>{{ ucfirst($pejabat->jabatan->nama) }} {{ ucfirst(strtolower($pejabat->skpd->nama)) }}</td>
            </tr>
        </table>
    </div>
    {{-- <br> --}}
    <div class="isi">Dengan ini menerangkan atas pernyataan yang bersangkutan bahwa :</div>
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
                <td class="kiri">Keterangan</td>
                <td class="titik">:</td>
                <td>Benar-benar dalam keadaan miskin.</td>
            </tr>
            <tr>
                <td class="titik">k.</td>
                <td class="kiri">Kategori</td>
                <td class="titik">:</td>
                <td>{{ $surat->kategori }}</td>
            </tr>
        </table>
    </div>
    {{-- <br> --}}
    <div class="num">2. Surat Keterangan Kelurahan ini diberikan :</div>
    <div>
        @if ($surat->jenis == 'perorangan')
            <table class="isi" style="width: 675px;">
                <tr>
                    <td class="titik">a.</td>
                    <td class="kiri">Nama</td>
                    <td class="titik">:</td>
                    <td>{{ $penduduk['name'] }}</td>
                </tr>
                <tr>
                    <td class="titik">b.</td>
                    <td class="kiri">Untuk Keperluan</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->peruntukan }}</td>
                </tr>
            </table>
        @else
            <table class="isi" style="width: 675px;">
                <tr>
                    <td class="titik">a.</td>
                    <td class="kiri">Nama</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->kepada }}</td>
                </tr>
                <tr>
                    <td class="titik"></td>
                    <td class="kiri">Tempat/Tanggal Lahir</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->kepada_tempat_lhr }}, {{ strtoupper($surat->kepada_tgl_lhr) }}</td>
                </tr>
                <tr>
                    <td class="titik"></td>
                    <td class="kiri">Sekolah di</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->kepada_sekolah }} kelas/semester {{ $surat->kepada_kelas }}</td>
                </tr>
                <tr>
                    <td class="titik"></td>
                    <td class="kiri">Jenis Kelamin</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->kepada_gender_nm }}</td>
                </tr>
                <tr>
                    <td class="titik"></td>
                    <td class="kiri">Status Keluarga</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->kepada_hubungan }}</td>
                </tr>
                <tr>
                    <td class="titik">b.</td>
                    <td class="kiri">Untuk Keperluan</td>
                    <td class="titik">:</td>
                    <td>{{ $surat->peruntukan }}</td>
                </tr>
            </table>
        @endif

    </div>
    {{-- <br> --}}
    <div class="num">3. Demikian Surat Keterangan Kelurahan ini dibuat untuk dipergunakan seperlunya.</div>
    <br>
    <div>
        <table class="text-center" style="width: 100%;margin-left:60px;">
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
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td style="color:grey;font-size:10;">
                    Telah ditantatangani secara elektronik
                    {{-- @if ($url != '')
                        <img src="data:image/png;base64, {!! $url !!} " style="width: 100px">
                    @else
                        <img src="img/placeholder.png" alt="" style="width: 100px">
                    @endif --}}
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="text-decoration: underline;font-weight: bold;">{{ $pejabat->nama }}</td>
            </tr>
            <tr>
                <td>{{ $penduduk['name'] }}</td>
                <td>NIP. {{ $pejabat->nip }}</td>
            </tr>
        </table>
        <br>
        <table class="text-center" style="width: 100%">
            <tr>
                <td>Register : {{ $nomorSurat }}</td>
            </tr>
            <tr>
                <td>Tanggal : Kediri, {{ $tglSurat }}</td>
            </tr>
            <tr>
                <td>Mengetahui,</td>
            </tr>
            <tr>
                <td>Camat Kecamatan</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="color:grey;font-size:10;">
                    Telah ditantatangani secara elektronik</td>
            </tr>
            <tr>
                <td style="text-decoration: underline;font-weight: bold;">{{ $pejabat->nama }}</td>
            </tr>
            <tr>
                <td>NIP. {{ $pejabat->nip }}</td>
            </tr>
        </table>
    </div>
    <div>
        @if ($url != '')
            <img class="qr" src="data:image/png;base64, {!! $url !!} ">
        @else
            <img class="qr" src="img/placeholder.png" alt="">
        @endif
        <img class="footer" src="img/catatan.png" alt="">
    </div>
</body>

</html>

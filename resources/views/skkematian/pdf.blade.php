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

        .border-luar {
            height: 1px;
            border: 3px solid black;
            margin-top: -10px;
            margin-left: 27px;
            margin-right: 27px;
        }

        .border-kotak {
            height: 1px;
            border: 1.5px solid black;
            padding-left: 2px;
        }

        .box {
            width: 6px;
            padding-left: 10px;
            padding-right: 10px;
            border: 1.5px solid black;
        }

        .box-tgl-kiri {
            width: 6px;
            padding-left: 10px;
            padding-right: 10px;
            border-left: 1.5px solid black;
        }

        .box-tgl-kanan {
            width: 6px;
            padding-left: 10px;
            padding-right: 10px;
            border-left: 1.5px solid black;
            border-right: 1.5px solid black;
        }

        .box-jenis {
            width: 6px;
            padding-left: 10px;
            padding-right: 10px;
            border-left: 1.5px solid black;
            border-right: 1.5px solid black;
            border-bottom: 1.5px solid black;
        }

        .jenis-pelaporan {
            width: 275px;
            padding-left: 10px;
        }

        .box-kode {
            padding-top: 4px;
            padding-bottom: 2px;
            padding-left: 32px;
            padding-right: 32px;
            border: 1px solid black;
        }

        .divA {
            height: 1px;
            padding-bottom: 3px;
            border-bottom: 3px solid black;
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
            width: 200px;
            /* padding-left: 10px; */
        }

        .titik {
            width: 1px;
            padding-right: 5px;
        }

        /* td {
            vertical-align: top;
        } */

        .footer {
            width: 100%;
            /* height: 150px; */
            position: fixed;
            bottom: 0px;
            left: 0px;
            margin-bottom: -30px;
        }

        .content {
            padding-left: 10px;
            /* padding-bottom: 10px; */
        }

        table,
        th,
        td {
            border-collapse: collapse;
            vertical-align: middle;
            padding: 0;
            /* border: 1px solid black; */
        }
    </style>
</head>

<body class="font-arial border-luar" style="font-size: 12px;">
    <div align="right" style="margin-right:40px;">
        <span class="box-kode">{{ __('F-2.01') }}</span>
    </div>
    <table class="content" style="width: 100%;">
        <tr>
            <td class="kiri">{{ __('Provinsi') }}</td>
            <td class="titik">{{ __(':') }}</td>
            <td class="divB">{{ __('JAWA TIMUR') }}</td>
        </tr>
        <tr>
            <td class="kiri">{{ __('Kabupaten/Kota') }}</td>
            <td class="titik">{{ __(':') }}</td>
            <td class="divB">{{ __('KOTA KEDIRI') }}</td>
        </tr>
        <tr>
            <td class="kiri">{{ __('Kecamatan') }}</td>
            <td class="titik">{{ __(':') }}</td>
            <td class="divB">{{ __('KOTA KEDIRI') }}</td>
        </tr>
        <tr>
            <td class="kiri">{{ __('Kelurahan') }}</td>
            <td class="titik">{{ __(':') }}</td>
            <td class="divB">{{ __('KOTA KEDIRI') }}</td>
        </tr>
        <tr>
            <td class="kiri">{{ __('Kode Wilayah') }}</td>
            <td class="titik">{{ __(':') }}</td>
            <td class="divB">{{ __('3571') }}</td>
        </tr>
    </table>
    <br>
    <div class="text-center" style="font-weight: bold;">
        {{ __('FORMULIR PELAPORAN PENCATATAN SIPIL DI DALAM WILAYAH NKRI') }}</div>
    <div style="padding-left:10px;">Jenis Pelaporan Pencatatan Sipil</div>
    <div class="content">
        <table style="width: 650px;">
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Kelahiran') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pengakuan Anak') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Lahir Mati') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pengesahan Anak') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Perkawinan') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Perubahan Nama') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pembatalan Perkawinan') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Perubahan Status Kewarganegaraan') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Perceraian') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pencatatan Peristiwa Penting Lainnya') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pembatalan Perceraian') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pembetulan Akta') }}</td>
            </tr>
            <tr>
                <td class="box">x</td>
                <td class="jenis-pelaporan">{{ __('Kematian') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pembatalan Akta') }}</td>
            </tr>
            <tr>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pengangkatan Anak') }}</td>
                <td class="box">&nbsp;</td>
                <td class="jenis-pelaporan">{{ __('Pelaporan Pencatatan Sipil dari Luar Wilayah NKRI') }}</td>
            </tr>
        </table>
    </div>
    <div class="divA"></div>
    <div class="content">
        <div style="font-weight: bold;">DATA PELAPOR</div>
        <table style="width: 650px; ">
            <tr>
                <td class="kiri">Nama</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_pelapor }}</td>
            </tr>
            <tr>
                <td class="kiri">NIK</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik_pelapor }}</td>
            </tr>
            <tr>
                <td class="kiri">Nomor Dokumen Perjalanan *</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->no_dokumen_perjalanan }}</td>
            </tr>
            <tr>
                <td class="kiri">Nomor Kartu Keluarga</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kk_pelapor }}</td>
            </tr>
            <tr>
                <td class="kiri">Kewarganegaraan</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kewarganegaraan_pelapor_nm }}</td>
            </tr>
        </table>
    </div>
    <div class="divA"></div>
    <div class="divA"></div>
    <div class="content">
        <div style="font-weight: bold;">DATA SAKSI I</div>
        <table style="width: 650px;">
            <tr>
                <td class="kiri">Nama</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_saksi1 }}</td>
            </tr>
            <tr>
                <td class="kiri">NIK</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik_saksi1 }}</td>
            </tr>
            <tr>
                <td class="kiri">Nomor Kartu Keluarga</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kk_saksi1 }}</td>
            </tr>
            <tr>
                <td class="kiri">Kewarganegaraan</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kewarganegaraan_saksi1_nm }}</td>
            </tr>
        </table>
        {{-- </div> --}}
        <div style="font-weight: bold;">DATA SAKSI II</div>
        {{-- <div class="content"> --}}
        <table style="width: 650px;">
            <tr>
                <td class="kiri">Nama</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_saksi2 }}</td>
            </tr>
            <tr>
                <td class="kiri">NIK</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik_saksi2 }}</td>
            </tr>
            <tr>
                <td class="kiri">Nomor Kartu Keluarga</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kk_saksi2 }}</td>
            </tr>
            <tr>
                <td class="kiri">Kewarganegaraan</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kewarganegaraan_saksi2_nm }}</td>
            </tr>
        </table>
    </div>
    <div class="divA"></div>
    <div class="divA"></div>
    <div style="font-weight: bold;padding-left:10px;">DATA ORANG TUA</div>
    <div class="content">
        <table style="width: 650px;">
            <tr>
                <td class="kiri">Nama Ayah</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_ayah }}</td>
            </tr>
            <tr>
                <td class="kiri">NIK Ayah</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik_ayah }}</td>
            </tr>
            <tr>
                <td class="kiri">Tempat Lahir Ayah</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->tempat_lhr_ayah }}</td>
            </tr>
            <tr>
                <td class="kiri">Tanggal Lahir Ayah</td>
                <td class="titik">:</td>
                <td>
                    <table>
                        <tr>
                            <td style="padding-left:10px;padding-right:10px;">Tgl :</td>
                            <td>
                                <div class="box-tgl-kiri">{{ $d_lhr_ayah[0] }}</div>
                            </td>
                            <td>
                                <div class="box-tgl-kanan">{{ $d_lhr_ayah[1] }}</div>
                            </td>
                            <td style="padding-left:10px;padding-right:10px;">Bln :</td>
                            <td>
                                <div class="box-tgl-kiri">{{ $m_lhr_ayah[0] }}</div>
                            </td>
                            <td>
                                <div class="box-tgl-kanan">{{ $m_lhr_ayah[1] }}</div>
                            </td>
                            <td style="padding-left:10px;padding-right:10px;">Thn :</td>
                            <td>
                                <div class="box-tgl-kiri">{{ $y_lhr_ayah[0] }}</div>
                            </td>
                            <td>
                                <div class="box-tgl-kiri">{{ $y_lhr_ayah[1] }}</div>
                            </td>
                            <td>
                                <div class="box-tgl-kiri">{{ $y_lhr_ayah[2] }}</div>
                            </td>
                            <td>
                                <div class="box-tgl-kanan">{{ $y_lhr_ayah[3] }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Kewarganegaraan</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kewarganegaraan_ayah_nm }}</td>
            </tr>
            <tr>
                <td class="kiri">Nama Ibu</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_ibu }}</td>
            </tr>
            <tr>
                <td class="kiri">NIK Ibu</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik_ibu }}</td>
            </tr>
            <tr>
                <td class="kiri">Tempat Lahir Ibu</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->tempat_lhr_ibu }}</td>
            </tr>
            <tr>
                <td class="kiri">Tanggal Lahir Ibu</td>
                <td class="titik">:</td>
                <table>
                    <tr>
                        <td style="padding-left:10px;padding-right:10px;">Tgl :</td>
                        <td>
                            <div class="box-tgl-kiri">{{ $d_lhr_ibu[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan">{{ $d_lhr_ibu[1] }}</div>
                        </td>
                        <td style="padding-left:10px;padding-right:10px;">Bln :</td>
                        <td>
                            <div class="box-tgl-kiri">{{ $m_lhr_ibu[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan">{{ $m_lhr_ibu[1] }}</div>
                        </td>
                        <td style="padding-left:10px;padding-right:10px;">Thn :</td>
                        <td>
                            <div class="box-tgl-kiri">{{ $y_lhr_ibu[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri">{{ $y_lhr_ibu[1] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri">{{ $y_lhr_ibu[2] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan">{{ $y_lhr_ibu[3] }}</div>
                        </td>
                    </tr>
                </table>
            </tr>
            <tr>
                <td class="kiri">Kewarganegaraan</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->kewarganegaraan_ibu_nm }}</td>
            </tr>
        </table>
    </div>
    <div class="divA"></div>
    <div class="divA"></div>
    <div style="font-weight: bold;padding-left:10px;">KEMATIAN</div>
    <div class="content">
        <table style="width: 650px;">
            <tr>
                <td class="kiri">NIK</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nik }}</td>
            </tr>
            <tr>
                <td class="kiri">Nama Lengkap</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama }}</td>
            </tr>
            <tr>
                <td class="kiri">Tanggal Kematian</td>
                <td class="titik">:</td>
                <table>
                    <tr>
                        <td style="padding-left:5px;padding-right:5px;">Tgl </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $d_kematian[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $d_kematian[1] }}</div>
                        </td>
                        <td style="padding-left:5px;padding-right:5px;">Bln</td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $m_kematian[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $m_kematian[1] }}</div>
                        </td>
                        <td style="padding-left:5px;padding-right:5px;">Thn</td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_kematian[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_kematian[1] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_kematian[2] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_kematian[3] }}</div>
                        </td>
                    </tr>
                </table>
            </tr>
            <tr>
                <td class="kiri">Pukul</td>
                <td class="titik">:</td>
                <td>
                    <table>
                        <tr>
                            <td class="box">{{ $hh_kematian }}</td>
                            <td>:</td>
                            <td class="box">{{ $mm_kematian }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri" style="vertical-align: top;">Sebab Kematian</td>
                <td class="titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->sebab_kematian == 'Sakit Biasa/Tua' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('1. Sakit Biasa/Tua') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->sebab_kematian == 'Wabah Penyakit' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('2. Wabah Penyakit') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->sebab_kematian == 'Kecelakaan' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('3. Kecelakaan') }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->sebab_kematian == 'Kriminalitas' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('4. Kriminalitas') }}</td>
                            <td class="box-jenis">{{ $surat->sebab_kematian == 'Bunuh Diri' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('5. Bunuh Diri') }}</td>
                            <td class="box-jenis">{{ $surat->sebab_kematian == 'Lainnya' ? 'x' : '' }}</td>
                            <td style="width: 110px;padding-left:5px;">{{ __('6. Lainnya') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Tempat kematian</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->tempat_kematian }}</td>
            </tr>
            <tr>
                <td class="kiri" style="vertical-align: top;">Yang menerangkan</td>
                <td class="titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->yang_menerangkan == 'Dokter' ? 'x' : '' }}</td>
                            <td style="width: 60px;padding-left:5px;">{{ __('Dokter') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->yang_menerangkan == 'Bidan/Perawat' ? 'x' : '' }}</td>
                            <td style="width: 90px;padding-left:5px;">{{ __('Bidan/Perawat') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->yang_menerangkan == 'Dukun' ? 'x' : '' }}</td>
                            <td style="width: 70px;padding-left:5px;">{{ __('Dukun') }}</td>
                            <td class="box-jenis">{{ $surat->yang_menerangkan == 'Lainnya' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('Lainnya') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="divA"></div>
    <br>
    <div>
        <table class="text-center" style="width: 100%">
            <tr>
                <td style="width: 50%;">Mengetahui,</td>
                <td style="width: 50%;">Kediri, {{ $tglSurat }}</td>
            </tr>
            <tr>
                <td>{{ ucfirst($pejabat->jabatan->nama) }} {{ ucfirst(strtolower($pejabat->skpd->nama)) }}</td>
                <td>Pemohon</td>
            </tr>
            <tr>
                <td style="padding: 10px">

                    @if ($url != '')
                        <img src="data:image/png;base64, {!! $url !!} " style="width: 100px">
                    @else
                        <img src="img/placeholder.png" alt="" style="width: 100px">
                    @endif
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-decoration: underline;font-weight: bold;">{{ $pejabat->nama }}</td>
                <td>{{ $surat->nama_pelapor }}</td>
            </tr>
            <tr>
                <td>NIP. {{ $pejabat->nip }}</td>
                <td></td>
            </tr>
        </table>
    </div>
    {{-- <div>
        <img class="footer" src="img/catatan.png" alt="" style="width: 100%">
    </div> --}}
</body>

</html>

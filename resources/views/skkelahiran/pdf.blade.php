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
                <td class="box">x</td>
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
                <td class="box">&nbsp;</td>
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
    <div style="font-weight: bold;padding-left:10px;">DATA ANAK</div>
    <div class="content">
        <table style="width: 650px;">
            <tr>
                <td class="kiri">Nama</td>
                <td class="titik">:</td>
                <td class="border-kotak">{{ $surat->nama_anak }}</td>
            </tr>
            <tr>
                <td class="kiri">Jenis Kelamin</td>
                <td class="titik">:</td>
                <td>
                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->gender_anak == 1 ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('1. Laki-laki') }}</td>
                            <td class="box-jenis">{{ $surat->gender_anak == 2 ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('2. Perempuan') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri" style="vertical-align: top;">Tempat Dilahirkan</td>
                <td class="titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->tempat_dilahirkan_anak == 'RS/RB' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('1. RS/RB') }}</td>
                            <td class="box-jenis">{{ $surat->tempat_dilahirkan_anak == 'Puskesmas' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('2. Puskesmas') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->tempat_dilahirkan_anak == 'Polindes' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('3. Polindes') }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->tempat_dilahirkan_anak == 'Rumah' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('4. Rumah') }}</td>
                            <td class="box-jenis">{{ $surat->tempat_dilahirkan_anak == 'Lainnya' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('5. Lainnya') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Tempat Kelahiran</td>
                <td class="titik">:</td>
                <td>{{ $surat->tempat_kelahiran_anak }}</td>
            </tr>
            <tr>
                <td class="kiri">Hari dan Tanggal Lahir</td>
                <td class="titik">:</td>
                <table>
                    <tr>
                        <td style="padding-right:5px;width:35px;">Hari </td>
                        <td class="border-kotak" style="width: 70px;">
                            {{ $surat->hari_lhr_anak }}
                        </td>
                        <td style="padding-left:5px;padding-right:5px;">Tgl </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $d_lhr_anak[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $d_lhr_anak[1] }}</div>
                        </td>
                        <td style="padding-left:5px;padding-right:5px;">Bln</td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $m_lhr_anak[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $m_lhr_anak[1] }}</div>
                        </td>
                        <td style="padding-left:5px;padding-right:5px;">Thn</td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_lhr_anak[0] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_lhr_anak[1] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kiri"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_lhr_anak[2] }}</div>
                        </td>
                        <td>
                            <div class="box-tgl-kanan"
                                style="border-bottom: 1.5px solid black;border-top: 1.5px solid black;">
                                {{ $y_lhr_anak[3] }}</div>
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
                            <td class="box">{{ $hh_lhr_anak }}</td>
                            <td>:</td>
                            <td class="box">{{ $mm_lhr_anak }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri" style="vertical-align: top;">Jenis Kelahiran</td>
                <td class="titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->jenis_klhr_anak == 'Tunggal' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('1. Tunggal') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->jenis_klhr_anak == 'Kembar 2' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('2. Kembar 2') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->jenis_klhr_anak == 'Kembar 3' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('3. Kembar 3') }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->jenis_klhr_anak == 'Kembar 4' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('4. Kembar 4') }}</td>
                            <td class="box-jenis">{{ $surat->jenis_klhr_anak == 'Lainnya' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('5. Lainnya') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Kelahiran ke</td>
                <td class="titik">:</td>
                <table>
                    <tr>
                        <td class="box">{{ $num_padded[0] }}</td>
                        <td class="box">{{ $num_padded[1] }}</td>
                    </tr>
                </table>
            </tr>
            <tr>
                <td class="kiri" style="vertical-align: top;">Penolong Kelahiran</td>
                <td class="titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->penolong_klhr_anak == 'Dokter' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('1. Dokter') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->penolong_klhr_anak == 'Bidan/Perawat' ? 'x' : '' }}</td>
                            <td style="width: 120px;padding-left:5px;">{{ __('2. Bidan/Perawat') }}</td>
                            <td class="box-jenis" style="border-top:1px solid black;">
                                {{ $surat->penolong_klhr_anak == 'Dukun' ? 'x' : '' }}</td>
                            <td style="width: 100px;padding-left:5px;">{{ __('3. Dukun') }}</td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <td class="box-jenis">{{ $surat->penolong_klhr_anak == 'Lainnya' ? 'x' : '' }}</td>
                            <td style="width: 80px;padding-left:5px;">{{ __('4. Lainnya') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Berat Bayi</td>
                <td class="titik">:</td>

                <td>
                    <table>
                        <tr>
                            <td class="box" style="width: 50px;">{{ $surat->bb_anak }}</td>
                            <td style="padding-left:5px;padding-right:5px;">kg</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="kiri">Panjang Bayi</td>
                <td class="titik">:</td>

                <td>
                    <table>
                        <tr>
                            <td class="box" style="width: 50px;">{{ $surat->tb_anak }}</td>
                            <td style="padding-left:5px;padding-right:5px;">cm</td>
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
            <tr >
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

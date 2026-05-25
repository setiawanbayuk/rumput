<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Verifikasi Dokumen E-Suket Kota Kediri' }}</title>
    <style>
        :root{
            --red:#b41218;
            --red-2:#7d0b10;
            --gold:#f5c84c;
            --gold-2:#ffe8a3;
            --ink:#111827;
            --soft:#64748b;
            --line:#e6e8ef;
            --green:#159947;
            --orange:#d97706;
            --danger:#dc2626;
            --card:#ffffff;
            --bg:#f7f2e8;
            --blue:#0f2747;
        }
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family:Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            color:var(--ink);
            background:
                radial-gradient(circle at 16% 8%, rgba(245,200,76,.25), transparent 32%),
                radial-gradient(circle at 90% 8%, rgba(180,18,24,.16), transparent 28%),
                linear-gradient(135deg, #fffdf7 0%, var(--bg) 48%, #fff 100%);
            min-height:100vh;
        }
        .page{position:relative; overflow:hidden; min-height:100vh; padding:28px 18px 38px;}
        .page:before,
        .page:after{
            content:""; position:fixed; width:320px; height:320px; border-radius:999px; z-index:-1; filter:blur(2px);
        }
        .page:before{left:-150px; top:120px; background:rgba(180,18,24,.10);}
        .page:after{right:-170px; bottom:80px; background:rgba(245,200,76,.25);}
        .shell{max-width:1180px; margin:0 auto;}
        .top-ribbon{
            position:relative; overflow:hidden; border-radius:30px; color:#fff;
            background:
                linear-gradient(135deg, rgba(125,11,16,.97), rgba(180,18,24,.94) 48%, rgba(15,39,71,.96)),
                radial-gradient(circle at 70% 20%, rgba(245,200,76,.35), transparent 28%);
            box-shadow:0 28px 70px rgba(125,11,16,.22);
            border:1px solid rgba(255,255,255,.18);
        }
        .top-ribbon:before{
            content:""; position:absolute; inset:-2px;
            background:linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
            transform:skewX(-18deg) translateX(-100%);
            animation:shine 5.5s infinite;
        }
        @keyframes shine{0%,60%{transform:skewX(-18deg) translateX(-120%)} 100%{transform:skewX(-18deg) translateX(120%)}}
        .hero{position:relative; z-index:1; display:grid; grid-template-columns:1.1fr .9fr; gap:24px; padding:28px; align-items:center;}
        .brand{display:flex; gap:16px; align-items:center; margin-bottom:24px;}
        .logo{
            width:74px; height:74px; object-fit:contain; background:#fff; border-radius:22px;
            padding:8px; box-shadow:0 16px 38px rgba(0,0,0,.22);
        }
        .brand small{display:block; font-weight:900; letter-spacing:.12em; text-transform:uppercase; color:var(--gold-2); font-size:12px; margin-bottom:4px;}
        .brand strong{font-size:24px; letter-spacing:.02em;}
        .headline{font-size:46px; line-height:1.05; letter-spacing:-.045em; margin:0 0 14px; font-weight:1000;}
        .headline span{color:var(--gold-2);}
        .lead{margin:0; max-width:680px; color:rgba(255,255,255,.86); font-size:16px; line-height:1.75;}
        .badges{display:flex; flex-wrap:wrap; gap:10px; margin-top:22px;}
        .badge{display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.22); font-weight:850; font-size:13px;}
        .badge.gold{background:rgba(245,200,76,.22); border-color:rgba(245,200,76,.42); color:#fff7cf;}
        .mascot-area{position:relative; min-height:245px; display:flex; align-items:flex-end; justify-content:center;}
        .mascot-bg{position:absolute; width:270px; height:270px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); box-shadow:inset 0 0 0 14px rgba(255,255,255,.05);}
        .mascot{position:relative; max-height:260px; max-width:48%; object-fit:contain; filter:drop-shadow(0 20px 26px rgba(0,0,0,.36));}
        .mascot.female{transform:translateX(24px); z-index:2;}
        .mascot.male{transform:translateX(-18px); z-index:1;}
        .layout{display:grid; grid-template-columns:minmax(0,1.12fr) minmax(320px,.88fr); gap:20px; margin-top:22px; align-items:start;}
        .card{background:rgba(255,255,255,.92); border:1px solid rgba(230,232,239,.95); border-radius:28px; box-shadow:0 24px 60px rgba(17,24,39,.09); overflow:hidden; backdrop-filter:blur(10px);}
        .card-head{display:flex; justify-content:space-between; gap:14px; align-items:center; padding:20px 22px; border-bottom:1px solid var(--line); background:linear-gradient(180deg, #ffffff, #fffaf0);}
        .card-title{display:flex; align-items:center; gap:10px; margin:0; font-size:19px; font-weight:1000; letter-spacing:-.02em;}
        .seal{display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:15px; color:#fff; background:linear-gradient(135deg,var(--red),var(--red-2)); box-shadow:0 10px 22px rgba(180,18,24,.22); font-size:16px; font-weight:1000;}
        .status{white-space:nowrap; border-radius:999px; padding:9px 13px; font-size:12px; font-weight:950; letter-spacing:.05em; text-transform:uppercase;}
        .status.ok{color:#0d7836; background:#dcfce7; border:1px solid #b7f3ca;}
        .status.wait{color:#9a5a00; background:#fff7db; border:1px solid #ffe08a;}
        .status.bad{color:#b91c1c; background:#fee2e2; border:1px solid #fecaca;}
        .card-body{padding:22px;}
        .verify-panel{display:grid; grid-template-columns:auto 1fr; gap:16px; align-items:start; padding:18px; border-radius:24px; border:1px solid #dbe7ff; background:linear-gradient(135deg,#f8fbff,#fffdf7); margin-bottom:18px;}
        .verify-panel.warning{border-color:#f8d992; background:linear-gradient(135deg,#fff7db,#fff);}
        .verify-panel.danger{border-color:#fecaca; background:linear-gradient(135deg,#fff1f2,#fff);}
        .check{width:56px; height:56px; border-radius:20px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:1000; font-size:24px; background:linear-gradient(135deg,#159947,#36c76c); box-shadow:0 16px 32px rgba(21,153,71,.22);}
        .verify-panel.warning .check{background:linear-gradient(135deg,#d97706,#f59e0b);}
        .verify-panel.danger .check{background:linear-gradient(135deg,#dc2626,#ef4444);}
        .verify-title{margin:0 0 7px; font-size:22px; font-weight:1000; letter-spacing:-.025em;}
        .verify-text{margin:0; color:#475569; line-height:1.7; font-size:14px;}
        .privacy-box{margin:16px 0 18px; border-radius:22px; padding:16px 18px; background:linear-gradient(135deg,#fff8e6,#ffffff); border:1px solid #f2dd9b; display:grid; grid-template-columns:auto 1fr; gap:14px; align-items:start;}
        .privacy-icon{width:44px; height:44px; border-radius:16px; background:linear-gradient(135deg,var(--blue),#244c7a); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:1000;}
        .privacy-title{font-weight:1000; margin-bottom:4px;}
        .privacy-text{color:#5b6473; line-height:1.65; font-size:13px; margin:0;}
        .grid{display:grid; grid-template-columns:1fr 1fr; gap:13px;}
        .info{position:relative; border:1px solid var(--line); background:#fff; border-radius:20px; padding:16px 17px; min-height:82px; overflow:hidden;}
        .info:after{content:""; position:absolute; right:-28px; top:-28px; width:72px; height:72px; border-radius:999px; background:rgba(245,200,76,.12);}
        .info.full{grid-column:1 / -1;}
        .label{font-size:11px; color:#6b7280; letter-spacing:.105em; text-transform:uppercase; font-weight:950; margin-bottom:8px;}
        .value{font-size:16px; font-weight:950; line-height:1.35; word-break:break-word; position:relative; z-index:1;}
        .masked{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; letter-spacing:.015em;}
        .signers{display:grid; gap:14px;}
        .signer{position:relative; border:1px solid var(--line); border-radius:23px; padding:18px; background:linear-gradient(135deg,#fff,#fffaf0); overflow:hidden;}
        .signer:before{content:""; position:absolute; inset:0 auto 0 0; width:6px; background:linear-gradient(var(--red),var(--gold));}
        .role{padding-left:8px; color:var(--red); font-size:12px; letter-spacing:.11em; text-transform:uppercase; font-weight:1000; margin-bottom:8px;}
        .name{padding-left:8px; font-size:22px; font-weight:1000; letter-spacing:-.025em; line-height:1.15;}
        .meta{padding-left:8px; margin-top:13px; display:grid; gap:8px; color:#475569; font-size:14px; line-height:1.45;}
        .note{margin-top:16px; padding:16px 17px; border-radius:22px; background:#f8fafc; border:1px dashed #cbd5e1; color:#475569; font-size:13px; line-height:1.7;}
        .watermark{position:absolute; right:28px; bottom:10px; color:#0f172a; opacity:.035; font-size:66px; font-weight:1000; letter-spacing:-.08em; pointer-events:none;}
        .side-card{position:relative;}
        .footer{margin:22px auto 0; text-align:center; color:#64748b; font-size:13px; line-height:1.7;}
        .not-found{max-width:780px; margin:70px auto 0;}
        @media(max-width:960px){
            .hero{grid-template-columns:1fr;}
            .mascot-area{display:none;}
            .layout{grid-template-columns:1fr;}
            .headline{font-size:38px;}
        }
        @media(max-width:620px){
            .page{padding:14px 10px 26px;}
            .top-ribbon,.card{border-radius:23px;}
            .hero{padding:22px 18px;}
            .brand{align-items:flex-start;}
            .logo{width:62px; height:62px; border-radius:18px;}
            .headline{font-size:30px;}
            .card-head{align-items:flex-start; flex-direction:column; padding:18px;}
            .card-body{padding:18px;}
            .grid{grid-template-columns:1fr;}
            .verify-panel,.privacy-box{grid-template-columns:1fr;}
            .status{white-space:normal;}
        }
    </style>
</head>
<body>
<div class="page">
    <main class="shell">
        @if(empty($found))
            <section class="card not-found">
                <div class="card-head">
                    <h1 class="card-title"><span class="seal">!</span> Dokumen Tidak Ditemukan</h1>
                    <span class="status bad">Tidak Valid</span>
                </div>
                <div class="card-body">
                    <div class="verify-panel danger">
                        <div class="check">!</div>
                        <div>
                            <h2 class="verify-title">QR/Barcode tidak terdaftar</h2>
                            <p class="verify-text">Tautan yang dipindai tidak ditemukan pada arsip verifikasi E-Suket Kota Kediri. Pastikan dokumen berasal dari kanal resmi pemerintah.</p>
                        </div>
                    </div>
                    <div class="privacy-box">
                        <div class="privacy-icon">ID</div>
                        <div>
                            <div class="privacy-title">Perlindungan Data Pribadi Aktif</div>
                            <p class="privacy-text">Laman publik ini tidak membuka NIK, isi keperluan surat, keterangan lainnya, maupun dokumen PDF langsung.</p>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <section class="top-ribbon">
                <div class="hero">
                    <div>
                        <div class="brand">
                            @if(!empty($assets['logo']))
                                <img class="logo" src="{{ $assets['logo'] }}" alt="Logo Kota Kediri">
                            @else
                                <div class="logo"></div>
                            @endif
                            <div>
                                <small>Pemerintah Kota Kediri</small>
                                <strong>ESUKET KOTA KEDIRI</strong>
                            </div>
                        </div>
                        <h1 class="headline">Verifikasi <span>Dokumen Resmi</span></h1>
                        
                        <div class="badges">
                            <!--<span class="badge gold">✓ Terverifikasi Arsip E-Suket</span>-->
                        </div>
                    </div>
                    <div class="mascot-area">
                        <div class="mascot-bg"></div>
                        @if(!empty($assets['asn_perempuan']))
                            <img class="mascot female" src="{{ $assets['asn_perempuan'] }}" alt="ASN Perempuan Kota Kediri">
                        @endif
                        @if(!empty($assets['asn_laki']))
                            <img class="mascot male" src="{{ $assets['asn_laki'] }}" alt="ASN Laki-Laki Kota Kediri">
                        @endif
                    </div>
                </div>
            </section>

            <section class="layout">
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title"><span class="seal">✓</span> Hasil Verifikasi</h2>
                        <span class="status {{ $isFinal ? 'ok' : 'wait' }}">{{ $statusName }}</span>
                    </div>
                    <div class="card-body">
                        <div class="verify-panel {{ $isFinal ? '' : 'warning' }}">
                            <div class="check">{{ $isFinal ? '✓' : '!' }}</div>
                            <div>
                                <h3 class="verify-title">{{ $isFinal ? 'Dokumen Resmi Terverifikasi' : 'Dokumen Ditemukan, Namun Belum Final' }}</h3>
                                <p class="verify-text">
                                    @if($isFinal)
                                        Data dasar dokumen cocok dengan arsip E-Suket Kota Kediri.
                                    @else
                                        Data surat ditemukan, tetapi proses tanda tangan/verifikasi belum final. Gunakan dokumen yang sudah selesai diproses oleh pejabat berwenang.
                                    @endif
                                </p>
                            </div>
                        </div>

<!--                        <div class="privacy-box">
                            <div class="privacy-icon">🔒</div>
                            <div>
                                <div class="privacy-title">Mode Publik Aman</div>
                                <p class="privacy-text">NIK pemohon, keperluan surat, keterangan keperluan lainnya, dan akses PDF langsung tidak ditampilkan pada halaman scan QR/Barcode.</p>
                            </div>
                        </div>-->

                        <div class="grid">
                            <div class="info full">
                                <div class="label">Nomor Surat</div>
                                <div class="value">{{ $nomorSurat }}</div>
                            </div>
                            <div class="info">
                                <div class="label">Jenis Surat</div>
                                <div class="value">{{ $jenisLabel }}</div>
                            </div>
                            <div class="info">
                                <div class="label">Tanggal Surat</div>
                                <div class="value">{{ $tanggalSurat }}</div>
                            </div>
                            <div class="info full">
                                <div class="label">Nama Pemohon</div>
                                <div class="value masked">{{ $namaPemohon }}</div>
                            </div>
                            <div class="info">
                                <div class="label">Kelurahan Penerbit</div>
                                <div class="value">{{ strtoupper(optional($skpd)->nama ?? '-') }}</div>
                            </div>
                            <div class="info">
                                <div class="label">Kecamatan</div>
                                <div class="value">{{ $kecamatanName ?: '-' }}</div>
                            </div>
                            @if(!empty($signedAt))
                                <div class="info full">
                                    <div class="label">Waktu TTE / Verifikasi</div>
                                    <div class="value">{{ $signedAt }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="card side-card">
                    <div class="card-head">
                        <h2 class="card-title"><span class="seal">ID</span> Penandatangan</h2>
                    </div>
                    <div class="card-body">
                        <div class="signers">
                            @forelse($signers as $signer)
                                <div class="signer">
                                    <div class="role">{{ $signer['sebagai'] }}</div>
                                    <div class="name masked">{{ $signer['nama'] }}</div>
                                    <div class="meta">
                                        <div><strong>Jabatan:</strong> {{ $signer['jabatan'] }}</div>
                                        <div><strong>NIP:</strong> <span class="masked">{{ $signer['nip'] }}</span></div>
                                        <div><strong>Wilayah:</strong> {{ $signer['wilayah'] ?: '-' }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="note">Data pejabat penandatangan belum ditemukan. Silakan cek data pejabat sesuai SKPD penerbit.</div>
                            @endforelse
                        </div>

                        <div class="note">
                            <strong>Catatan resmi:</strong> Halaman ini hanya untuk validasi keaslian dokumen. Untuk melihat isi lengkap surat, gunakan dokumen PDF resmi yang diterima dari kanal pelayanan, bukan dari halaman publik scan QR/Barcode.
                        </div>
                    </div>
                    <div class="watermark">E-SUKET</div>
                </aside>
            </section>

            <div class="footer">
                © {{ date('Y') }} Pemerintah Kota Kediri · Sistem Informasi E-Suket Kota Kediri<br>
                Verifikasi publik dengan perlindungan data pribadi pemohon.
            </div>
        @endif
    </main>
</div>
</body>
</html>

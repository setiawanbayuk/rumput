{{-- Scan KTP khusus admin role_id 1. Semua pemrosesan foto dilakukan di browser. --}}
<div id="esuket-ktp-scanner" class="mb-3 ktp-compact" aria-label="Scan KTP untuk Tambah Surat">
    <style>
        #esuket-ktp-scanner{--ktp-accent:#806938;--ktp-border:#d9cba9;color:#354052}
        /* V8: dua tombol di form; kamera HP tetap tampil di ESUKET dengan bingkai putih. */
        #esuket-ktp-scanner .ktp-toolbar{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        #esuket-ktp-scanner .ktp-toolbar .ktp-button{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-width:126px;padding:9px 13px;font-weight:650;box-shadow:none}
        #esuket-ktp-scanner .ktp-toolbar .ktp-primary{background:#149e96;border-color:#149e96;color:#fff}
        #esuket-ktp-scanner .ktp-toolbar .ktp-primary:hover{background:#0d817a;border-color:#0d817a}
        #esuket-ktp-scanner .ktp-toolbar .ktp-secondary{color:#127e79;border-color:#addbd8;background:#fff}
        #esuket-ktp-scanner .ktp-toolbar .ktp-secondary:hover{background:#e9f7f5}
        #esuket-ktp-scanner .ktp-camera-notice{margin-top:8px;padding:10px 12px;border-radius:9px;font-size:.81rem;line-height:1.5;color:#8e2d25;background:#fff3f0;border:1px solid #f4c6bd}
        #esuket-ktp-scanner .ktp-box{display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:2502;width:min(650px,calc(100vw - 24px));max-height:88dvh;overflow-y:auto;overscroll-behavior:contain;box-shadow:0 20px 70px rgba(0,0,0,.28)}
        #esuket-ktp-scanner:not(.ktp-compact)>.ktp-box{display:block}
        #esuket-ktp-scanner .ktp-overlay{position:fixed;inset:0;z-index:2501;background:rgba(18,30,42,.42)}
        #esuket-ktp-scanner.ktp-compact>.ktp-overlay{display:none}
        #esuket-ktp-scanner .ktp-dialog-close{border:1px solid #cabb98;background:#fff;color:#564726;border-radius:9px;font-size:.85rem;padding:7px 12px}
        #esuket-ktp-scanner .ktp-dialog-title{font-size:.91rem;font-weight:700;color:#4d432f}
        #esuket-ktp-scanner .ktp-compact-bar{margin-top:6px;padding:6px 0;border:none;background:transparent}
        #esuket-ktp-scanner .ktp-compact-bar .ktp-button{display:none!important}
        #esuket-ktp-scanner .ktp-compact-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        #esuket-ktp-scanner .ktp-compact-text{font-size:.76rem;line-height:1.4;flex:1 1 230px;overflow-wrap:anywhere;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden}
        #esuket-ktp-scanner .ktp-compact-bar .progress{width:100%;height:5px;margin-top:1px}
        #esuket-ktp-scanner .ktp-box{border:1px solid var(--ktp-border);border-radius:16px;background:linear-gradient(135deg,#fffdfa,#faf5e9);padding:16px}
        #esuket-ktp-scanner .ktp-title{font-size:1.04rem;font-weight:750;color:#4d432f}
        #esuket-ktp-scanner .ktp-subtitle{font-size:.81rem;line-height:1.55;color:#655f52}
        #esuket-ktp-scanner .ktp-button{border-radius:9px;padding:8px 12px;font-weight:600;font-size:.84rem}
        #esuket-ktp-scanner .ktp-primary{background:#806938;color:#fff;border:1px solid #806938}
        #esuket-ktp-scanner .ktp-primary:hover{background:#685429;color:#fff}
        #esuket-ktp-scanner .ktp-secondary{background:#fff;color:#564726;border:1px solid #cabb98}
        #esuket-ktp-scanner .ktp-secondary:hover{background:#f5eddf}
        #esuket-ktp-scanner .ktp-stage{position:relative;max-width:100%;border-radius:10px;border:2px solid #d4c3a0;overflow:hidden;background:#f0f2f4}
        #esuket-ktp-scanner #ktp-stage{display:block;width:100%;height:auto;aspect-ratio:85.6/54;touch-action:none;cursor:crosshair}
        #esuket-ktp-scanner .ktp-video-frame{position:relative;width:100%;aspect-ratio:85.6/54;overflow:hidden;border-radius:10px;background:#16212b}
        #esuket-ktp-scanner .ktp-video{display:block;width:100%;height:100%;object-fit:cover}
        #esuket-ktp-scanner .ktp-video-frame video::-webkit-media-controls{display:none!important}
        #esuket-ktp-scanner .ktp-video-frame video::-webkit-media-controls-start-playback-button{display:none!important}
        #esuket-ktp-scanner .ktp-guide{position:absolute;inset:8%;border:2px dashed rgba(255,255,255,.94);box-shadow:0 0 0 999px rgba(0,0,0,.38);border-radius:9px;pointer-events:none;transition:box-shadow .2s ease}
        #esuket-ktp-scanner .ktp-guide-corner{position:absolute;width:26px;height:26px;border:solid #fff;border-width:0;filter:drop-shadow(0 1px 2px #0008)}
        #esuket-ktp-scanner .ktp-guide-corner.tl{top:-2px;left:-2px;border-top-width:5px;border-left-width:5px;border-top-left-radius:8px}
        #esuket-ktp-scanner .ktp-guide-corner.tr{top:-2px;right:-2px;border-top-width:5px;border-right-width:5px;border-top-right-radius:8px}
        #esuket-ktp-scanner .ktp-guide-corner.bl{bottom:-2px;left:-2px;border-bottom-width:5px;border-left-width:5px;border-bottom-left-radius:8px}
        #esuket-ktp-scanner .ktp-guide-corner.br{bottom:-2px;right:-2px;border-bottom-width:5px;border-right-width:5px;border-bottom-right-radius:8px}
        #esuket-ktp-scanner.ktp-guide-ready .ktp-guide{box-shadow:0 0 0 999px rgba(0,0,0,.2),0 0 0 3px rgba(12,180,115,.65)}
        #esuket-ktp-scanner #ktp-capture:disabled{opacity:.5;cursor:not-allowed}
        #esuket-ktp-scanner .ktp-camera-hint{position:absolute;left:9%;right:9%;bottom:10%;color:white;font-weight:700;text-shadow:0 1px 4px #000;font-size:.8rem;text-align:center;pointer-events:none}
        #esuket-ktp-scanner .ktp-result{border-top:1px solid var(--ktp-border);margin-top:15px;padding-top:14px}
        #esuket-ktp-scanner .ktp-edit-row{display:grid;grid-template-columns:minmax(90px,40%) minmax(0,1fr);align-items:center;gap:8px;padding:6px 0;border-bottom:1px dashed #e9dfcb}
        #esuket-ktp-scanner .ktp-edit-row label{font-size:.79rem;color:#655f52}
        #esuket-ktp-scanner .ktp-edit-row input{width:100%;min-width:0;font-size:.83rem;border-radius:7px;background:#fff}
        #esuket-ktp-scanner .ktp-edit-row input::placeholder{font-size:.73rem;color:#90959b}
        #esuket-ktp-scanner .ktp-status{font-size:.84rem;line-height:1.55;overflow-wrap:anywhere}
        #esuket-ktp-scanner .ktp-warning{font-size:.8rem;color:#9e572d;padding-left:1.2rem;margin-bottom:0}
        #esuket-ktp-scanner .ktp-expanded{position:fixed;inset:3vh 3vw;z-index:2503;background:#fff;border:2px solid #806938;box-shadow:0 16px 60px #0007;overflow:auto;border-radius:16px;padding:22px!important}
        #esuket-ktp-scanner .ktp-expanded .ktp-stage{width:min(100%,1050px);margin:auto}
        #esuket-ktp-scanner .ktp-expanded .ktp-crop-tools{justify-content:center}
        #esuket-ktp-scanner .ktp-expanded .ktp-preview-heading{text-align:center;font-size:1.04rem}
        #esuket-ktp-scanner button:focus-visible{outline:3px solid #d1a84e;outline-offset:2px}
        /* Ruang kamera lebih besar pada HP; garis putih tetap di atas video dalam dialog. */
        @media(max-width:767px){
            #esuket-ktp-scanner .ktp-box{width:100vw;max-width:none;height:100dvh;max-height:100dvh;left:0;top:0;transform:none;border-radius:0;overflow-y:auto;overscroll-behavior:contain;padding:14px;padding-top:max(14px,env(safe-area-inset-top));padding-bottom:max(14px,env(safe-area-inset-bottom))}
            #esuket-ktp-scanner .ktp-video-frame{width:100%;max-height:70dvh}
            #esuket-ktp-scanner .ktp-guide-corner{width:32px;height:32px}
            #esuket-ktp-scanner .ktp-camera-hint{font-size:.72rem}
        }
        @media(max-width:420px){#esuket-ktp-scanner .ktp-box{padding:11px;padding-top:max(11px,env(safe-area-inset-top));padding-bottom:max(11px,env(safe-area-inset-bottom));max-height:100dvh}#esuket-ktp-scanner .ktp-toolbar .ktp-button{min-width:0;flex:1 1 44%;padding:9px 7px}#esuket-ktp-scanner .ktp-edit-row{grid-template-columns:minmax(83px,35%) minmax(0,1fr)}#esuket-ktp-scanner .ktp-expanded{inset:1vh 1vw;padding:10px!important}}
    </style>
    <div class="ktp-toolbar" role="group" aria-label="Scan KTP untuk mengisi form Tambah Surat">
        <button id="ktp-open-camera" type="button" class="btn ktp-button ktp-primary"><i class="ri-camera-line" aria-hidden="true"></i> Scan Kamera</button>
        <button id="ktp-open-file" type="button" class="btn ktp-button ktp-secondary"><i class="ri-image-add-line" aria-hidden="true"></i> Upload KTP</button>
        <input id="ktp-file" type="file" accept="image/jpeg,image/png" class="d-none" aria-label="Unggah foto KTP JPG atau PNG">
    </div>
    <div id="ktp-camera-notice" class="ktp-camera-notice d-none" role="alert" aria-live="assertive"></div>
    <div id="ktp-dialog-overlay" class="ktp-overlay d-none" aria-hidden="true"></div>
    <div class="ktp-box" role="dialog" aria-modal="true" aria-label="Kamera dan koreksi pembacaan KTP">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <div class="ktp-dialog-title">Kamera / Koreksi KTP</div>
            <button id="ktp-close-panel" type="button" class="ktp-dialog-close">Tutup <span aria-hidden="true">×</span></button>
        </div>
        <div id="ktp-camera-area" class="d-none mb-3">
            <div class="ktp-video-frame">
                <video id="ktp-video" class="ktp-video" autoplay playsinline webkit-playsinline muted aria-label="Pratinjau kamera dengan bingkai KTP"></video>
                <div class="ktp-guide" aria-hidden="true">
                    <span class="ktp-guide-corner tl"></span><span class="ktp-guide-corner tr"></span>
                    <span class="ktp-guide-corner bl"></span><span class="ktp-guide-corner br"></span>
                </div>
                <div class="ktp-camera-hint">Wajib: KTP utuh dan tepat di dalam garis putih</div>
            </div>
            <p id="ktp-camera-quality" class="ktp-subtitle mt-2 mb-1" aria-live="polite">Menunggu KTP di dalam garis putih…</p>
            <p id="ktp-camera-focus-help" class="ktp-subtitle mb-2"></p>
            <div class="d-flex gap-2 mt-2 flex-wrap">
                <button id="ktp-capture" class="btn ktp-button ktp-primary" type="button" disabled title="Aktif setelah KTP tepat di bingkai dan gambar cukup jelas">Ambil Foto</button>
                <button id="ktp-refocus" class="btn ktp-button ktp-secondary" type="button">Coba Fokus</button>
                <button id="ktp-close-camera" class="btn ktp-button ktp-secondary" type="button">Tutup Kamera</button>
            </div>
            <div class="form-check mt-2">
                <input id="ktp-auto-capture" type="checkbox" class="form-check-input" checked>
                <label for="ktp-auto-capture" class="form-check-label small">Ambil otomatis hanya ketika empat sisi KTP sesuai bingkai, fokus, dan stabil</label>
            </div>
        </div>
        <div id="ktp-image-area" class="d-none mb-3">
            <div class="ktp-preview-heading ktp-subtitle mb-2"><strong>Atur batas KTP:</strong> seret bingkai putih untuk memindahkan; klik <em>Buat Bingkai Baru</em> untuk menggambar ulang sesuai batas KTP.</div>
            <div class="ktp-stage">
                <canvas id="ktp-stage" width="1100" height="694" aria-label="Pratinjau foto dan area crop KTP"></canvas>
            </div>
            <div class="d-flex gap-2 flex-wrap mt-2 ktp-crop-tools">
                <button id="ktp-new-crop" class="btn ktp-button ktp-secondary" type="button">Buat Bingkai Baru</button>
                <button id="ktp-reset-crop" class="btn ktp-button ktp-secondary" type="button">Reset Bingkai</button>
                <button id="ktp-enlarge" class="btn ktp-button ktp-secondary" type="button">Perbesar Foto</button>
                <button id="ktp-read" class="btn ktp-button ktp-primary" type="button">Baca KTP</button>
            </div>
            <div class="d-flex justify-content-between gap-2 flex-wrap mt-2 align-items-center">
                <small id="ktp-filename" class="text-muted"></small>
                <div class="d-flex gap-2">
                    <button id="ktp-rescan" class="btn ktp-button ktp-secondary" type="button">Baca Ulang</button>
                    <button id="ktp-clear" class="btn ktp-button ktp-secondary" type="button">Hapus Foto</button>
                </div>
            </div>
        </div>
        <div id="ktp-progress-wrap" class="progress mb-2 d-none" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height:7px">
            <div id="ktp-progress" class="progress-bar" style="background:#806938;width:0"></div>
        </div>
        <div id="ktp-status" class="ktp-status text-muted" role="status" aria-live="polite">Pilih kamera atau unggah JPG/PNG KTP terlebih dahulu.</div>
        <div id="ktp-results" class="ktp-result d-none">
            <div class="fw-bold mb-1" style="font-size:.92rem">Hasil OCR KTP — bisa dikoreksi</div>
            <div class="ktp-subtitle mb-2">Kolom di bawah merupakan hasil baca gambar, bukan data yang telah diverifikasi. Koreksi sebelum menyalin ke Tambah Surat.</div>
            <div id="ktp-detected"></div>
            <p id="ktp-not-filled" class="ktp-subtitle mt-2 mb-1"></p>
            <ul id="ktp-warnings" class="ktp-warning"></ul>
            <details class="mt-2">
                <summary class="ktp-subtitle" style="cursor:pointer">Tampilkan teks mentah OCR untuk memeriksa kolom yang belum terbaca</summary>
                <pre id="ktp-raw-text" class="small mt-2 p-2" style="white-space:pre-wrap;overflow-wrap:anywhere;background:#fff;border:1px solid #e2d8c3;border-radius:8px;max-height:220px;overflow:auto"></pre>
            </details>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="ktp-auto-fill" checked>
                <label class="form-check-label small" for="ktp-auto-fill">Isi otomatis kolom Tambah Surat setelah NIK dan nama berhasil terbaca tanpa konflik. Ini bukan menyimpan atau mengajukan surat.</label>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="ktp-fill-masters" checked>
                <label class="form-check-label small" for="ktp-fill-masters">Cocokkan otomatis dengan pilihan master database (agama, gender, pekerjaan, status, wilayah hingga RT/RW). Yang tidak cocok tetap kosong untuk dipilih manual.</label>
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="ktp-overwrite">
                <label class="form-check-label small" for="ktp-overwrite">Timpa nilai yang sudah ada pada form Tambah Surat</label>
            </div>
            <button id="ktp-apply" type="button" class="btn ktp-button ktp-primary mt-2">Isi ke Form Tambah Surat</button>
            <p id="ktp-fill-note" class="ktp-subtitle mt-2 mb-0"></p>
        </div>
        <div class="ktp-subtitle mt-3"><i class="ri-shield-check-line me-1" aria-hidden="true"></i>Foto diproses di browser, bukan dikirim sebagai berkas surat. OCR memerlukan internet untuk memuat model pada penggunaan awal. No. KK dan pendidikan tidak tercantum di KTP dan perlu dilengkapi manual. Pemindaian tidak otomatis menekan tombol Simpan Surat.</div>
    </div>
    <div id="ktp-compact-bar" class="ktp-compact-bar d-none" role="group" aria-label="Status pemindaian KTP">
        <span id="ktp-compact-status" class="ktp-compact-text text-muted" role="status" aria-live="polite">Memproses foto KTP…</span>
        <button id="ktp-compact-review" type="button" class="btn btn-sm ktp-button ktp-secondary d-none">Tinjau hasil OCR</button>
        <button id="ktp-compact-reopen" type="button" class="btn btn-sm ktp-button ktp-secondary">Scan ulang</button>
        <div id="ktp-compact-progress-wrap" class="progress d-none" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div id="ktp-compact-progress" class="progress-bar" style="background:#806938;width:0"></div></div>
    </div>
</div>

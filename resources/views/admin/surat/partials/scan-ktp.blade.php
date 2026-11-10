{{-- OCR hanya membaca KTP; elemen, API master, dan logika form tetap dikelola ESUKET. --}}
<div id="esuket-ktp-scanner" class="mb-3" aria-label="Scan KTP untuk mengisi data warga">
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" id="ktp-photo-btn" class="btn btn-sm text-white" style="background:#148F85;border-radius:8px;padding:9px 14px">
            <i class="ri-camera-line me-1" aria-hidden="true"></i> Ambil Foto KTP
        </button>
        <button type="button" id="ktp-upload-btn" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:9px 14px">
            <i class="ri-image-add-line me-1" aria-hidden="true"></i> Upload KTP
        </button>
        {{-- Kamera berbingkai khusus HTTPS. --}}
        <input id="ktp-upload-file" type="file" accept="image/jpeg,image/png,image/webp" class="d-none" aria-label="Unggah foto KTP">
    </div>
    {{-- Panel kamera HTTPS terisolasi. Seluruh tampilan field/Select2 dan alur Upload V16 tetap asli. --}}
    <style>
        #esuket-ktp-scanner .ktp-camera-modal[hidden], #esuket-ktp-scanner [hidden] {display:none!important}
        #esuket-ktp-scanner .ktp-camera-modal{position:fixed;inset:0;z-index:11050;background:rgba(5,17,27,.88);display:flex;align-items:center;justify-content:center;padding:12px}
        #esuket-ktp-scanner .ktp-camera-panel{width:min(580px,100%);max-height:calc(100dvh - 24px);overflow:auto;background:#fff;color:#253240;border-radius:15px;padding:15px;box-shadow:0 20px 55px #0008}
        #esuket-ktp-scanner .ktp-camera-view{position:relative;width:100%;aspect-ratio:85.6/54;background:#13212d;overflow:hidden;border-radius:11px;isolation:isolate}
        #esuket-ktp-scanner .ktp-camera-video{width:100%;height:100%;display:block;object-fit:cover}
        #esuket-ktp-scanner .ktp-camera-guide{position:absolute;inset:6%;z-index:1;pointer-events:none;border:2px solid rgba(255,255,255,.95);border-radius:8px;box-shadow:0 0 0 99vmax rgba(0,0,0,.23)}
        #esuket-ktp-scanner .ktp-camera-corner{position:absolute;width:23px;height:23px;border:solid #fff;border-width:0;filter:drop-shadow(0 1px 2px #0008)}
        #esuket-ktp-scanner .ktp-camera-corner.tl{top:-3px;left:-3px;border-top-width:5px;border-left-width:5px;border-top-left-radius:7px}
        #esuket-ktp-scanner .ktp-camera-corner.tr{top:-3px;right:-3px;border-top-width:5px;border-right-width:5px;border-top-right-radius:7px}
        #esuket-ktp-scanner .ktp-camera-corner.bl{bottom:-3px;left:-3px;border-bottom-width:5px;border-left-width:5px;border-bottom-left-radius:7px}
        #esuket-ktp-scanner .ktp-camera-corner.br{bottom:-3px;right:-3px;border-bottom-width:5px;border-right-width:5px;border-bottom-right-radius:7px}
        #esuket-ktp-scanner .ktp-camera-hint{position:absolute;z-index:2;bottom:10px;left:8%;right:8%;text-align:center;color:white;font-size:.76rem;line-height:1.3;font-weight:650;text-shadow:0 1px 5px #000}
        #esuket-ktp-scanner .ktp-camera-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
        #esuket-ktp-scanner .ktp-camera-actions button{flex:1 1 auto}
        body.ktp-camera-open{overflow:hidden}
        /* Hanya tampilan loading V19: animasi jam pasir; alur OCR tetap sama. */
        #esuket-ktp-scanner .ktp-ocr-hourglass{display:inline-flex;width:25px;height:25px;align-items:center;justify-content:center;transform-origin:center;animation:ktp-ocr-hourglass-turn 1.8s ease-in-out infinite}
        @keyframes ktp-ocr-hourglass-turn{0%,15%{transform:rotate(0deg)}45%,55%{transform:rotate(180deg)}85%,100%{transform:rotate(360deg)}}
        @media(prefers-reduced-motion:reduce){#esuket-ktp-scanner .ktp-ocr-hourglass{animation:none}}
        @media(max-width:600px){#esuket-ktp-scanner .ktp-camera-modal{padding:0;align-items:stretch}#esuket-ktp-scanner .ktp-camera-panel{width:100%;height:100dvh;max-height:100dvh;border-radius:0;display:flex;flex-direction:column;justify-content:center;gap:7px;padding:14px;padding-top:max(14px,env(safe-area-inset-top));padding-bottom:max(14px,env(safe-area-inset-bottom))}#esuket-ktp-scanner .ktp-camera-view{flex-shrink:0}#esuket-ktp-scanner .ktp-camera-actions button{min-height:42px}}
    </style>
    <div id="ktp-camera-dialog" class="ktp-camera-modal" hidden role="dialog" aria-modal="true" aria-label="Ambil Foto KTP dengan bingkai">
        <div class="ktp-camera-panel">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                <strong class="small">Ambil Foto KTP</strong>
                <button type="button" id="ktp-camera-close" class="btn btn-sm btn-outline-secondary">Tutup</button>
            </div>
            <div id="ktp-camera-view" class="ktp-camera-view" aria-label="Bingkai kartu identitas">
                <video id="ktp-camera-video" class="ktp-camera-video" autoplay playsinline muted></video>
                <div id="ktp-camera-guide" class="ktp-camera-guide" aria-hidden="true">
                    <span class="ktp-camera-corner tl"></span><span class="ktp-camera-corner tr"></span>
                    <span class="ktp-camera-corner bl"></span><span class="ktp-camera-corner br"></span>
                </div>
                <span class="ktp-camera-hint">Pastikan keempat sudut KTP masuk bingkai</span>
            </div>
            <div id="ktp-camera-message" class="small text-muted mt-2" role="status" aria-live="polite">Arahkan KTP ke bingkai kamera.</div>
            <div class="ktp-camera-actions">
                <button type="button" id="ktp-camera-capture" class="btn btn-sm text-white" style="background:#148F85" disabled>Jepret &amp; Baca KTP</button>
            </div>
        </div>
    </div>
    <div id="ktp-status" role="status" aria-live="polite" class="small text-muted mt-2">Foto KTP akan dibaca otomatis dan digunakan untuk mengisi kolom yang cocok. Periksa hasil sebelum menyimpan.</div>
    <div id="ktp-progress-wrap" class="mt-2 d-none" role="status" aria-live="polite" aria-label="OCR sedang membaca KTP">
        <span class="d-inline-flex align-items-center gap-2">
            <span class="ktp-ocr-hourglass" aria-hidden="true">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#148F85" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                    <path d="M5 3h14M5 21h14M7 3v4a5 5 0 0 0 2 4l3 1 3-1a5 5 0 0 0 2-4V3M7 21v-4a5 5 0 0 1 2-4l3-1 3 1a5 5 0 0 1 2 4v4"/>
                    <path d="M9.4 7.4h5.2M10.5 17.8h3" stroke-width="1.2"/>
                </svg>
            </span>
            <span class="small text-muted">OCR sedang membaca KTP…</span>
        </span>
        {{-- Tetap ada karena JavaScript V19 mengatur width elemen ini; tampilannya saja disembunyikan. --}}
        <div id="ktp-progress" class="visually-hidden" style="width:0%" aria-hidden="true"></div>
    </div>
</div>

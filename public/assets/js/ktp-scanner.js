/* ESUKET OCR V16 + V19 kamera HTTPS dan Upload KTP optimal: tetap satu kali OCR foto asli. Tidak mengubah personal.js, Select2,
   form surat, atau lookup residents. Dropdown diisi hanya dengan option dari endpoint master yang cocok. */
(() => {
    'use strict';
    const TESSERACT_URL = 'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/tesseract.min.js';
    const MAX_BYTES = 15 * 1024 * 1024;
    const TEXT_IDS = ['nik', 'name', 'tempat_lhr', 'tgl_lhr', 'alamat'];
    const MASTER_IDS = ['gender', 'status_kwn', 'kewarganegaraan', 'agama', 'pekerjaan'];
    const LOCATIONS = [
        ['provinsi', null], ['kabko', 'kode_provinsi'], ['kecamatan', 'kode_kabkota'],
        ['kelurahan', 'kode_kecamatan'], ['rw', 'kode_kelurahan'], ['rt', 'rw']
    ];
    const PARENT = { kode_provinsi: 'provinsi', kode_kabkota: 'kabko', kode_kecamatan: 'kecamatan', kode_kelurahan: 'kelurahan', rw: 'rw' };
    const norm = v => String(v ?? '').toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^A-Z0-9]/g, '');

    // PENTING: komponen Select2 di-init personal.js pada DOM ready.
    // [hidden] menyebabkan ukuran dropdown nol. Ubah ke visibility:hidden SEBELUM
    // DOMContentLoaded agar element tetap punya lebar saat Select2 asli di-init.
    // Setelah scan, reveal() hanya membuka wrapper. Tidak menyentuh select2().
    document.querySelectorAll('[data-ktp-gated][hidden]').forEach(group => {
        group.style.visibility = 'hidden';
        group.style.maxHeight = '0px';
        group.style.minHeight = '0px';
        group.style.overflow = 'hidden';
        group.style.margin = '0';
        group.style.padding = '0';
        group.removeAttribute('hidden');
        group.dataset.ktpPending = '1';
    });

    document.addEventListener('DOMContentLoaded', () => {
        const root = document.getElementById('esuket-ktp-scanner');
        if (!root) return;
        const byId = id => document.getElementById(id);
        const statusEl = byId('ktp-status');
        const barWrap = byId('ktp-progress-wrap');
        const bar = byId('ktp-progress');
        let busy = false;
        let workerPromise = null;
        let scriptPromise = null;
        const ocrWritten = new Map();

        function status(text, error = false) {
            statusEl.textContent = text;
            statusEl.className = 'small mt-2 ' + (error ? 'text-danger' : 'text-muted');
        }
        function loading(value) {
            busy = value;
            byId('ktp-photo-btn').disabled = value;
            byId('ktp-upload-btn').disabled = value;
            barWrap.classList.toggle('d-none', !value);
            if (value) bar.style.width = '8%';
        }
        function reveal() {
            // Hanya mengubah penampakan wrapper, bukan elemen field, dropdown, atau event personal.js.
            document.querySelectorAll('[data-ktp-gated]').forEach(group => {
                group.removeAttribute('hidden');
                group.classList.remove('ktp-pending-scan');
                if (group.dataset.ktpPending === '1') {
                    ['visibility', 'max-height', 'min-height', 'overflow', 'margin', 'padding'].forEach(property => group.style.removeProperty(property));
                    delete group.dataset.ktpPending;
                }
            });
        }
        function clearPreviouslyWritten() {
            // Jangan menimpa koreksi yang sudah diubah manual oleh pengguna.
            for (const [id, value] of ocrWritten) {
                const field = byId(id);
                if (field && String(field.value) === String(value)) {
                    field.value = '';
                    if (field.tagName === 'SELECT') {
                        if (window.jQuery) window.jQuery(field).trigger('change');
                        else field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    const hidden = byId(id + '_nm');
                    if (hidden) hidden.value = '';
                }
            }
            ocrWritten.clear();
        }
        async function getTesseract() {
            if (window.Tesseract?.createWorker) return window.Tesseract;
            if (!scriptPromise) {
                scriptPromise = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = TESSERACT_URL;
                    script.async = true;
                    script.onload = () => window.Tesseract?.createWorker ? resolve(window.Tesseract) : reject(new Error('Mesin OCR tidak berhasil dimuat'));
                    script.onerror = () => reject(new Error('Mesin OCR tidak dapat diunduh. Periksa koneksi internet'));
                    document.head.appendChild(script);
                }).catch(error => { scriptPromise = null; throw error; });
            }
            return scriptPromise;
        }
        async function getWorker() {
            if (!workerPromise) {
                workerPromise = getTesseract().then(lib => lib.createWorker('ind+eng', 1, {
                    logger: m => { if (busy && m?.status === 'recognizing text' && typeof m.progress === 'number') bar.style.width = (10 + Math.floor(m.progress * 70)) + '%'; }
                })).catch(err => { workerPromise = null; throw err; });
            }
            return workerPromise;
        }
        function canWrite(id) {
                const field = byId(id);
                if (!field) return false;

                // Jangan biarkan OCR menimpa hasil CARI residents.
                const residentNik = window.__esuketResidentsAppliedNik;

                if (
                    residentNik &&
                    String(byId('nik')?.value || '').trim() ===
                    String(residentNik).trim()
                ) {
                    return false;
                }

                const old = ocrWritten.get(id);

                return !String(field.value || '').trim() ||
                    (
                        old !== undefined &&
                        String(field.value) === String(old)
                    );
            }
        function setText(id, value) {
            const field = byId(id);
            if (!field || !value || !canWrite(id)) return false;
            if (id === 'nik' && !/^\d{16}$/.test(String(value))) return false;
            if (id === 'name' && (!/^[A-Z\s.'\-]{3,60}$/i.test(String(value)) || /\b(?:PROVINSI|KECAMATAN|AGAMA|NIK)\b/i.test(String(value)))) return false;
            field.value = id === 'tgl_lhr' ? String(value) : String(value).toUpperCase();
            ocrWritten.set(id, String(field.value));
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }
        function canon(id, value) {
            const v = norm(value);
            if (id === 'kabko') {
                const m = v.match(/^(KABUPATEN|KAB|KOTA)(.*)$/);
                return m ? (m[1] === 'KOTA' ? 'KOTA:' : 'KAB:') + m[2] : '';
            }
            if (id === 'agama') return ({ KATOLIK: 'KATHOLIK', BUDDHA: 'BUDHA' })[v] || v;
            if (id === 'status_kwn') return v === 'MENIKAH' ? 'KAWIN' : v;
            if (id === 'pekerjaan') return v === 'KARYAWANSWASTA' ? 'PEGAWAISWASTA' : v;
            if (id === 'rw' || id === 'rt') return /^\d{1,3}$/.test(String(value)) ? String(Number(value)) : '';
            if (id === 'kewarganegaraan') return v === 'WARGANEGARAINDONESIA' ? 'WNI' : v;
            return v;
        }
        function matched(id, label, expected) {
            const a = canon(id, label), b = canon(id, expected);
            return !!a && a === b;
        }
        async function options(id, query, extra = {}) {
            const url = new URL('/api/' + id, location.origin);
            url.searchParams.set('q', query);
            for (const [key, value] of Object.entries(extra)) url.searchParams.set(key, value);
            const response = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) return [];
            const data = await response.json();
            return Array.isArray(data) ? data : Array.isArray(data.data) ? data.data : [];
        }
        async function setMaster(id, value, extra = {}) {
            const select = byId(id);
            if (!select || select.tagName !== 'SELECT' || !value || !canWrite(id)) return false;
            const terms = [String(value)];
            if (id === 'kabko') terms.push(String(value).replace(/^(KABUPATEN|KAB\.?|KOTA)\s+/i, ''));
            if (id === 'rw' || id === 'rt') terms.push(String(Number(value)));
            if (id === 'agama' && norm(value) === 'KATOLIK') terms.push('KATHOLIK');
            if (id === 'pekerjaan' && norm(value) === 'PEGAWAISWASTA') terms.push('KARYAWAN SWASTA');
            let result;
            for (const term of new Set(terms.filter(Boolean))) {
                const data = await options(id, term, extra);
                result = data.find(item => item && item.id != null && matched(id, item.text, value));
                if (result) break;
            }
            if (!result || !canWrite(id)) return false; // Jangan pernah mengarang kode master.
            let option = Array.from(select.options).find(option => String(option.value) === String(result.id));
            if (!option) {
                option = new Option(result.text, String(result.id));
                select.add(option); // Tambah satu pilihan valid tanpa membuang pilihan lama.
            }
            select.value = String(result.id);
            ocrWritten.set(id, String(select.value));
            const label = byId(id + '_nm');
            if (label) label.value = result.text;
            // Trigger event milik personal.js yang membangun URL kabko, kecamatan, dst.
            if (window.jQuery) window.jQuery(select).trigger('change');
            else select.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }
        async function applyData(data) {
            let count = 0;
            for (const id of TEXT_IDS) if (setText(id, data[id])) count++;
            for (const id of MASTER_IDS) {
                if (!data[id]) continue;
                try { if (await setMaster(id, data[id])) count++; } catch (e) { console.warn('OCR master', id, e); }
            }
            for (const [id, parentKey] of LOCATIONS) {
                if (!data[id]) continue;
                const extra = {};
                if (parentKey) {
                    const parentValue = byId(PARENT[parentKey])?.value;
                    if (!parentValue) continue;
                    extra[parentKey] = parentValue;
                    if (id === 'rt') extra.kode_kelurahan = byId('kelurahan')?.value || '';
                }
                try { if (await setMaster(id, data[id], extra)) count++; } catch (e) { console.warn('OCR wilayah', id, e); }
            }
            return count;
        }
        async function nikFallback(worker, file) {
            // Pemindaian NIK kedua hanya ketika OCR utama tidak menemukan NIK numerik yang tepat.
            const image = await createImageBitmap(file);
            try {
                const crop = document.createElement('canvas');
                crop.width = Math.min(2000, image.width);
                crop.height = Math.max(100, Math.round(crop.width * 0.24));
                const ctx = crop.getContext('2d');
                const sx = 0, sy = Math.round(image.height * 0.12);
                const sw = Math.round(image.width * 0.76);
                const sh = Math.round(image.height * 0.23);
                ctx.drawImage(image, sx, sy, sw, sh, 0, 0, crop.width, crop.height);
                await worker.setParameters({ tessedit_char_whitelist: '0123456789 ', tessedit_pageseg_mode: '6' });
                const response = await worker.recognize(crop);
                await worker.setParameters({ tessedit_char_whitelist: '', tessedit_pageseg_mode: '3' });
                const nik = String(response?.data?.text || '').match(/(?<!\d)\d{16}(?!\d)/)?.[0];
                return nik || '';
            } finally { image.close(); }
        }
        // Optimalisasi ringan KHUSUS gambar unggahan. Tidak mengubah warna, memotong
        // kartu, memakai filter scanner, atau menambah pemindaian OCR berulang.
        // Ukuran sangat besar dikurangi supaya lebih cepat. Foto ukuran normal
        // dipakai asli: upscaling terbukti bisa merusak karakter NIK pada contoh uji.
        async function prepareUpload(file) {
            let picture;
            let cleanup = () => {};
            try {
                if (typeof createImageBitmap === 'function') {
                    try {
                        picture = await createImageBitmap(file, { imageOrientation: 'from-image' });
                        cleanup = () => picture.close();
                    } catch (_) { /* gunakan HTML Image untuk perangkat tanpa dukungan bitmap */ }
                }
                if (!picture) {
                    const objectUrl = URL.createObjectURL(file);
                    cleanup = () => URL.revokeObjectURL(objectUrl);
                    picture = new Image();
                    picture.src = objectUrl;
                    await new Promise((resolve, reject) => {
                        if (picture.complete && picture.naturalWidth) return resolve();
                        picture.onload = resolve;
                        picture.onerror = () => reject(new Error('Foto KTP tidak dapat dibuka. Pilih JPG/PNG/WebP yang valid.'));
                    });
                }
                const width = picture.naturalWidth || picture.width;
                const height = picture.naturalHeight || picture.height;
                if (!width || !height) throw new Error('Ukuran foto KTP tidak valid.');
                const maxSide = Math.max(width, height);
                // Pertahankan file asli untuk <=3500px: uji KTP menunjukkan
                // memperbesar foto dapat mengurangi akurasi baca angka NIK.
                if (maxSide <= 3500) return {file, lowResolution: maxSide < 1100};
                const factor = 3000 / maxSide;
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.round(width * factor));
                canvas.height = Math.max(1, Math.round(height * factor));
                const context = canvas.getContext('2d', {alpha: false});
                if (!context) return {file, lowResolution: maxSide < 1100};
                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';
                context.drawImage(picture, 0, 0, canvas.width, canvas.height);
                const targetType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                const blob = await new Promise(resolve => canvas.toBlob(resolve, targetType, .95));
                if (!blob) return {file, lowResolution: maxSide < 1100};
                return {
                    file: new File([blob], 'ktp-upload-ocr' + (targetType === 'image/png' ? '.png' : '.jpg'), {type: targetType}),
                    lowResolution: maxSide < 1100
                };
            } finally { cleanup(); }
        }
        async function readPhoto(file, source = 'upload') {
            if (busy || !file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size === 0 || file.size > MAX_BYTES) {
                status('Gunakan foto JPG, PNG, atau WebP dengan ukuran maksimal 15 MB.', true); return;
            }
            window.__esuketResidentsAppliedNik = '';
            loading(true);
            reveal(); // Form tampil dan tetap bisa dilengkapi, walau OCR foto gagal.
            status('Membaca KTP, mohon periksa dan lengkapi kolom setelah proses selesai…');
            try {
                const worker = await getWorker();
                bar.style.width = '15%';
                const ready = source === 'upload' ? await prepareUpload(file) : {file, lowResolution: false};
                const ocrFile = ready.file;
                const response = await worker.recognize(ocrFile); // Satu pembacaan foto berwarna, tanpa filter scanner/crop.
                const parser = window.EsuketKtpParser;
                if (!parser || typeof parser.parse !== 'function') throw new Error('Parser OCR belum berhasil dimuat');
                const data = parser.parse(response?.data?.text || '');
                if (!data.nik) {
                    try { const nik = await nikFallback(worker, ocrFile); if (nik) data.nik = nik; } catch (_) { /* pembacaan tambahan NIK bila diperlukan */ }
                }
                bar.style.width = '85%';
                clearPreviouslyWritten();
                const count = await applyData(data);
                bar.style.width = '100%';
                const parts = [count + ' kolom terisi otomatis.'];
                if (!data.nik) parts.push('NIK belum terbaca tepat, periksa dan isi manual.');
                if (!data.name) parts.push('Nama belum terbaca jelas, isi manual.');
                if (ready.lowResolution) parts.push('Resolusi foto rendah; cek kembali hasil pembacaan.');
                parts.push('Dropdown yang kosong tetap bisa dicari seperti biasa. Cocokkan hasil dengan KTP sebelum menyimpan.');
                status(parts.join(' '));
            } catch (err) {
                status('OCR tidak berhasil: ' + (err?.message || 'gagal membaca foto') + '. Form tetap terbuka untuk dilengkapi setelah percobaan scan.', true);
            } finally {
                loading(false);
            }
        }
        // Kamera langsung berbingkai hanya untuk HTTPS; memakai OCR cepat V16.
        // Jalur Upload KTP, OCR/parsing, Select2, dan pengisian form TIDAK DIUBAH.
        const cameraDialog = byId('ktp-camera-dialog');
        const cameraVideo = byId('ktp-camera-video');
        const cameraFrame = byId('ktp-camera-view');
        const cameraGuide = byId('ktp-camera-guide');
        const cameraMessage = byId('ktp-camera-message');
        const cameraCapture = byId('ktp-camera-capture');
        let cameraStream = null;
        let cameraOpening = false;
        let cameraRun = 0; // Membatalkan getUserMedia yang selesai sesudah dialog ditutup.
        let focusReturn = null;

        function cameraInfo(message, error = false) {
            cameraMessage.textContent = message;
            cameraMessage.classList.toggle('text-danger', error);
        }
        function stopCamera() {
            cameraRun++;
            cameraOpening = false;
            cameraCapture.disabled = true;
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            cameraVideo.pause();
            cameraVideo.srcObject = null;
        }
        function closeCamera() {
            stopCamera();
            cameraDialog.hidden = true;
            document.body.classList.remove('ktp-camera-open');
            if (focusReturn && focusReturn.isConnected) focusReturn.focus();
        }
        function showCamera() {
            focusReturn = document.activeElement;
            cameraDialog.hidden = false;
            document.body.classList.add('ktp-camera-open');
            cameraCapture.disabled = true;
            byId('ktp-camera-close').focus();
        }
        async function openCamera() {
            if (busy || cameraOpening || !cameraDialog.hidden) return;
            // Fitur jepret hanya tersedia melalui HTTPS, tanpa kamera bawaan atau fallback.
            if (window.location.protocol !== 'https:' || !window.isSecureContext) {
                status('Ambil Foto KTP hanya tersedia melalui HTTPS.', true);
                return;
            }
            if (!navigator.mediaDevices?.getUserMedia) {
                status('Browser ini tidak mendukung kamera langsung.', true);
                return;
            }
            showCamera();
            const run = ++cameraRun;
            cameraOpening = true;
            cameraInfo('Membuka kamera belakang HP…');
            try {
                // Ideal besar untuk teks kecil; browser bebas memilih resolusi terdekat.
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: { facingMode: { ideal: 'environment' }, width: { ideal: 1920 }, height: { ideal: 1080 } }
                });
                if (cameraDialog.hidden || run !== cameraRun) {
                    stream.getTracks().forEach(track => track.stop());
                    return;
                }
                cameraStream = stream;
                cameraVideo.srcObject = stream;
                await cameraVideo.play();
                if (cameraDialog.hidden || run !== cameraRun) return;
                cameraCapture.disabled = !cameraVideo.videoWidth;
                cameraInfo('KTP siap difoto di dalam bingkai.');
            } catch (error) {
                if (cameraDialog.hidden || run !== cameraRun) return;
                cameraInfo(error?.name === 'NotAllowedError' ?
                    'Izin kamera ditolak. Aktifkan izin kamera di browser.' :
                    'Kamera gagal dibuka. Periksa izin kamera dan koneksi HTTPS.', true);
            } finally {
                if (run === cameraRun) cameraOpening = false;
            }
        }
        // Ambil area dalam bingkai dari frame video persis seperti yang terlihat,
        // termasuk koreksi object-fit:cover. Tidak ada filter atau scanner warna.
        function frameToJpeg() {
            const videoWidth = cameraVideo.videoWidth;
            const videoHeight = cameraVideo.videoHeight;
            if (!videoWidth || !videoHeight || !cameraStream?.active) throw new Error('Kamera belum siap. Coba lagi.');
            const view = cameraFrame.getBoundingClientRect();
            const guide = cameraGuide.getBoundingClientRect();
            if (!view.width || !view.height || !guide.width || !guide.height) throw new Error('Bingkai kamera tidak tersedia.');
            const scale = Math.max(view.width / videoWidth, view.height / videoHeight);
            const overflowX = (videoWidth * scale - view.width) / 2;
            const overflowY = (videoHeight * scale - view.height) / 2;
            const sx = Math.max(0, (guide.left - view.left + overflowX) / scale);
            const sy = Math.max(0, (guide.top - view.top + overflowY) / scale);
            const sw = Math.min(videoWidth - sx, guide.width / scale);
            const sh = Math.min(videoHeight - sy, guide.height / scale);
            if (sw < 400 || sh < 240) throw new Error('Resolusi kamera terlalu kecil untuk membaca KTP.');
            const canvas = document.createElement('canvas');
            canvas.width = Math.min(2400, Math.max(1200, Math.round(sw)));
            canvas.height = Math.round(canvas.width * sh / sw);
            const ctx = canvas.getContext('2d', { alpha: false });
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(cameraVideo, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
            return new Promise((resolve, reject) => {
                canvas.toBlob(blob => blob ? resolve(new File([blob], 'ktp-kamera.jpg', { type: 'image/jpeg' })) : reject(new Error('Gagal mengambil foto dari kamera')), 'image/jpeg', 0.95);
            });
        }
        async function captureCamera() {
            if (busy || cameraCapture.disabled) return;
            cameraCapture.disabled = true;
            cameraInfo('Mengambil foto KTP…');
            try {
                const shot = await frameToJpeg();
                closeCamera();
                // readPhoto adalah fungsi yang sama persis dengan tombol Upload pada V16.
                await readPhoto(shot, 'camera');
            } catch (error) {
                cameraInfo(error?.message || 'Gagal mengambil gambar.', true);
                if (cameraStream?.active) cameraCapture.disabled = false;
            }
        }
        byId('ktp-photo-btn').addEventListener('click', openCamera);
        cameraCapture.addEventListener('click', captureCamera);
        byId('ktp-camera-close').addEventListener('click', closeCamera);
        cameraDialog.addEventListener('click', event => { if (event.target === cameraDialog) closeCamera(); });
        cameraDialog.addEventListener('keydown', event => { if (event.key === 'Escape') closeCamera(); });
        byId('ktp-upload-btn').addEventListener('click', () => { byId('ktp-upload-file').value = ''; byId('ktp-upload-file').click(); });
        byId('ktp-upload-file').addEventListener('change', e => readPhoto(e.target.files?.[0]));
        document.addEventListener('visibilitychange', () => { if (document.hidden && cameraStream) closeCamera(); });
        window.addEventListener('pagehide', () => {
            stopCamera();
            if (workerPromise) workerPromise.then(worker => worker.terminate()).catch(() => {});
        });
    });
})();

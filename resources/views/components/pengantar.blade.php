@props(['pengantar' => null, 'readonly' => false, 'surat' => null, 'sumber' => null])

@php
    $buktiSurat = $surat ?? null;
    $pengantar = $pengantar ?: ($buktiSurat->pengantar ?? null);
    $sumberBukti = $sumber ?: ($buktiSurat ? (str_contains($buktiSurat->getTable(), 'pengajuans') ? 'pengajuan' : match ($buktiSurat->getTable()) {
        'surat_keterangans' => 'suket', 'surat_skbns' => 'skbn', 'surat_sktms' => 'sktm',
        'surat_domisilis' => 'skdom', 'surat_penghasilans' => 'skhsl', 'surat_usahas' => 'skusaha',
        'surat_boros' => 'skboro', 'surat_kelahirans' => 'skkelahiran', 'surat_kematians' => 'skkematian',
        default => null,
    }) : null);
    $buktiTambahan = $buktiSurat && $sumberBukti ? $buktiSurat->buktiPendukung()->orderBy('id')->get() : collect();
    $sudahAda = filled($pengantar) || $buktiTambahan->isNotEmpty();
    // Hanya insert atau edit sebelum diserahkan ke Sekkel. Tidak ada tombol X di detail/preview/TTE.
    $bisaUbahBukti = !$readonly && (!$buktiSurat || in_array((int) $buktiSurat->status, [0, 1], true));
    $urlPengantar = $pengantar && $buktiSurat && $sumberBukti
        ? route('bukti-pendukung.legacy-file', [$sumberBukti, $buktiSurat->id])
        : null;
@endphp
<div class="row mb-3" data-esuket-bukti-upload>
    <label for="bukti_pendukung" class="col-md-3 col-form-label text-md-start ms-2">
        Bukti Pendukung <span class="text-danger">*</span>
    </label>
    <div class="col-md-8">
        @if($bisaUbahBukti)
            <input class="form-control @error('bukti_pendukung') is-invalid @enderror @error('bukti_pendukung.*') is-invalid @enderror"
                   type="file" id="bukti_pendukung" name="bukti_pendukung[]"
                   accept="image/jpeg,image/png,image/webp,image/bmp,application/pdf" multiple
                   @if(!$sudahAda) required @endif>
            <div class="form-text">Minimal 1 Bukti Pendukung. Dapat memilih banyak JPG, PNG, WEBP, BMP, atau PDF sekaligus (maksimal 20 file per unggahan, masing-masing 5 MB). Klik X untuk menghapus pilihan sebelum disimpan.</div>
            @error('bukti_pendukung') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @error('bukti_pendukung.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            @error('bukti_pendukung_hapus') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            @error('bukti_pendukung_hapus_legacy') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            <div data-bukti-preview class="d-flex flex-wrap gap-3 mt-3" aria-live="polite"></div>
            <div data-bukti-delete-inputs></div>
            <div data-bukti-error class="text-danger small mt-2 d-none" role="alert"></div>
        @elseif(!$readonly)
            <div class="small text-muted">Bukti Pendukung sudah dikunci karena surat telah diteruskan dalam proses persetujuan.</div>
        @endif

        @if($sudahAda)
            <div class="d-flex flex-wrap gap-3 mt-3">
                @if($pengantar)
                    <div class="position-relative border rounded p-2" data-bukti-saved="legacy" style="width:155px;max-width:100%">
                        <a href="{{ $urlPengantar }}" target="_blank" rel="noopener noreferrer" title="Bukti Pendukung 1" class="text-decoration-none">
                            @if(str_ends_with(strtolower(parse_url($pengantar, PHP_URL_PATH) ?: $pengantar), '.pdf'))
                                <div class="d-flex justify-content-center align-items-center bg-light" style="height:85px"><i class="ri-file-pdf-line fs-3 text-danger"></i></div>
                            @else
                                <img src="{{ $urlPengantar }}" alt="Bukti Pendukung 1" class="w-100 rounded" style="height:85px;object-fit:contain;background:#f8fafc">
                            @endif
                            <div class="small text-dark text-truncate mt-1" title="Bukti Pendukung 1">Bukti Pendukung 1</div>
                        </a>
                        @if($bisaUbahBukti)
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0 shadow-sm"
                                    style="width:26px;height:26px;line-height:26px;transform:translate(35%,-35%);z-index:1"
                                    data-bukti-remove-saved title="Hapus Bukti Pendukung 1" aria-label="Hapus Bukti Pendukung 1">&times;</button>
                            <div data-bukti-remove-status class="d-none small text-danger mt-1">Dihapus saat disimpan</div>
                        @endif
                    </div>
                @endif
                @foreach($buktiTambahan as $bukti)
                    <div class="position-relative border rounded p-2" data-bukti-saved="{{ $bukti->id }}" style="width:155px;max-width:100%">
                        <a href="{{ route('bukti-pendukung.file', [$sumberBukti, $buktiSurat->id, $bukti->id]) }}" target="_blank" rel="noopener noreferrer" title="{{ $bukti->nama_asli }}" class="text-decoration-none">
                            @if($bukti->mime_type === 'application/pdf')
                                <div class="d-flex justify-content-center align-items-center bg-light" style="height:85px"><i class="ri-file-pdf-line fs-3 text-danger"></i></div>
                            @else
                                <img src="{{ route('bukti-pendukung.file', [$sumberBukti, $buktiSurat->id, $bukti->id]) }}" alt="{{ $bukti->nama_asli }}" loading="lazy" class="w-100 rounded" style="height:85px;object-fit:contain;background:#f8fafc">
                            @endif
                            <div class="small text-dark text-truncate mt-1" title="{{ $bukti->nama_asli }}">{{ $bukti->nama_asli ?: 'Bukti Pendukung' }}</div>
                        </a>
                        @if($bisaUbahBukti)
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0 shadow-sm"
                                    style="width:26px;height:26px;line-height:26px;transform:translate(35%,-35%);z-index:1"
                                    data-bukti-remove-saved title="Hapus Bukti Pendukung" aria-label="Hapus Bukti Pendukung">&times;</button>
                            <div data-bukti-remove-status class="d-none small text-danger mt-1">Dihapus saat disimpan</div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($buktiSurat && $sumberBukti)
                <a class="btn btn-outline-primary btn-sm mt-2" target="_blank" rel="noopener noreferrer"
                   href="{{ route('bukti-pendukung.show', [$sumberBukti, $buktiSurat->id]) }}">
                    <i class="ri-attachment-2 me-1"></i>Lihat Semua Bukti Pendukung
                </a>
            @endif
        @elseif($readonly)
            <div class="text-muted fst-italic">Belum ada Bukti Pendukung</div>
        @endif
    </div>
    @if($bisaUbahBukti)
    <script>
    (function () {
        const root = document.currentScript.closest('[data-esuket-bukti-upload]');
        const input = root && root.querySelector('input[type="file"][name="bukti_pendukung[]"]');
        const preview = root && root.querySelector('[data-bukti-preview]');
        const fields = root && root.querySelector('[data-bukti-delete-inputs]');
        const error = root && root.querySelector('[data-bukti-error]');
        if (!input || !preview || !fields || !error) return;

        let pilihan = [];
        const hapusTersimpan = new Set();
        const saved = Array.from(root.querySelectorAll('[data-bukti-saved]'));
        const remaining = () => saved.length - hapusTersimpan.size + pilihan.length;
        function showError(message) { error.textContent = message; error.classList.toggle('d-none', !message); }
        function syncFiles() {
            const transfer = new DataTransfer();
            pilihan.forEach(file => transfer.items.add(file));
            input.files = transfer.files;
            input.required = remaining() < 1;
        }
        function drawPreview() {
            preview.replaceChildren();
            pilihan.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'position-relative border rounded p-2 small';
                card.style.cssText = 'width:155px;min-height:110px';
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    const src = URL.createObjectURL(file);
                    img.src = src;
                    img.alt = file.name;
                    img.onload = img.onerror = () => URL.revokeObjectURL(src);
                    img.style.cssText = 'width:100%;height:85px;object-fit:contain';
                    card.appendChild(img);
                } else {
                    const icon = document.createElement('i');
                    icon.className = 'ri-file-pdf-line fs-4';
                    card.appendChild(icon);
                }
                const label = document.createElement('div');
                label.className = 'text-truncate mt-1';
                label.title = file.name;
                label.textContent = file.name;
                card.appendChild(label);
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0 shadow-sm';
                remove.style.cssText = 'width:26px;height:26px;line-height:26px;transform:translate(35%,-35%);z-index:1';
                remove.setAttribute('aria-label', 'Batalkan ' + file.name);
                remove.title = 'Hapus dari daftar upload';
                remove.textContent = '×';
                remove.addEventListener('click', () => {
                    pilihan.splice(index, 1);
                    syncFiles(); drawPreview(); showError('');
                });
                card.appendChild(remove); preview.appendChild(card);
            });
        }
        function syncDeleted() {
            fields.replaceChildren();
            hapusTersimpan.forEach(id => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = id === 'legacy' ? 'bukti_pendukung_hapus_legacy' : 'bukti_pendukung_hapus[]';
                hidden.value = id === 'legacy' ? '1' : id;
                fields.appendChild(hidden);
            });
            input.required = remaining() < 1;
            showError('');
        }
        input.addEventListener('invalid', () => {
            showError('Bukti Pendukung wajib minimal satu file. Tambahkan foto atau batalkan penghapusan.');
        });
        input.addEventListener('change', () => {
            const incoming = Array.from(input.files || []);
            // File input bawaan berubah tiap pilihan; kami pertahankan daftar sebelumnya.
            pilihan.push(...incoming);
            if (pilihan.length > 20) {
                pilihan = pilihan.slice(0, 20);
                showError('Maksimal 20 file baru dalam satu unggahan.');
            } else showError('');
            syncFiles(); drawPreview();
        });
        saved.forEach(item => {
            const button = item.querySelector('[data-bukti-remove-saved]');
            if (!button) return;
            const id = item.dataset.buktiSaved;
            const status = item.querySelector('[data-bukti-remove-status]');
            button.addEventListener('click', () => {
                if (hapusTersimpan.has(id)) {
                    hapusTersimpan.delete(id);
                    button.textContent = '×'; button.title = 'Hapus Bukti Pendukung';
                    button.classList.replace('btn-outline-secondary', 'btn-danger');
                    item.style.opacity = '';
                    status.classList.add('d-none');
                } else {
                    hapusTersimpan.add(id);
                    button.textContent = '↶'; button.title = 'Batalkan penghapusan';
                    button.classList.replace('btn-danger', 'btn-outline-secondary');
                    item.style.opacity = '0.55';
                    status.classList.remove('d-none');
                }
                syncDeleted();
            });
        });
        const form = root.closest('form');
        if (form) form.addEventListener('submit', event => {
            if (remaining() >= 1) return;
            event.preventDefault();
            showError('Bukti Pendukung wajib minimal satu file. Tambahkan foto atau batalkan penghapusan.');
            input.focus();
        });
    })();
    </script>
    @endif
</div>

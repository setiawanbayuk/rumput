/* Tampilan Preview + Bukti Pendukung. PDF tetap disajikan apa adanya dari endpoint existing.
   Galeri hanya dibaca lewat endpoint berizin; sama sekali tidak mengubah dokumen TTE. */
(function () {
    'use strict';

    let requestSerial = 0;

    function ensurePreviewModal() {
        let element = document.getElementById('esuketPreviewBuktiModal');
        if (element) return element;

        element = document.createElement('dialog');
        element.id = 'esuketPreviewBuktiModal';
        element.setAttribute('aria-label', 'Pratinjau surat dan Bukti Pendukung');
        element.style.cssText = 'width:min(1080px,96vw);max-width:96vw;max-height:92vh;border:0;border-radius:16px;padding:0;box-shadow:0 18px 65px rgba(0,0,0,.22);';
        element.innerHTML = `
            <div style="max-height:92vh;overflow-y:auto;background:#fff;color:#212529;">
                <div class="d-flex justify-content-between align-items-center border-bottom px-3 px-md-4 py-3 bg-white" style="position:sticky;top:0;z-index:1">
                    <h5 class="mb-0 fw-semibold"><i class="ri-eye-line me-2"></i><span data-esuket-title>Preview Surat</span></h5>
                    <button type="button" class="btn-close" data-esuket-close aria-label="Tutup"></button>
                </div>
                <div class="p-3 p-md-4">
                    <section data-esuket-pdf-block class="mb-4">
                        <div class="small text-muted fw-semibold mb-2">Dokumen Surat</div>
                        <iframe data-esuket-pdf class="w-100 border rounded-3 bg-light" style="height:min(63vh,740px);min-height:340px"
                                title="Pratinjau PDF surat" loading="lazy"></iframe>
                    </section>
                    <section data-esuket-gallery class="rounded-3 border p-3 p-md-4" aria-live="polite"></section>
                </div>
            </div>`;
        document.body.appendChild(element);
        element.querySelector('[data-esuket-close]').addEventListener('click', () => element.close());
        element.addEventListener('click', function (event) {
            if (event.target === element) element.close();
        });
        if (!document.getElementById('esuketPreviewBuktiDialogStyle')) {
            const style = document.createElement('style');
            style.id = 'esuketPreviewBuktiDialogStyle';
            style.textContent = '#esuketPreviewBuktiModal::backdrop{background:rgba(15,23,42,.72)}';
            document.head.appendChild(style);
        }

        element.addEventListener('close', function () {
            ++requestSerial;
            const frame = element.querySelector('[data-esuket-pdf]');
            frame.removeAttribute('src'); // Hentikan PDF saat modal ditutup
            element.querySelector('[data-esuket-gallery]').replaceChildren();
        });
        return element;
    }

    async function showPreviewBukti(pdfUrl, galleryUrl, onlyGallery) {
        if (!galleryUrl || typeof HTMLDialogElement === 'undefined') {
            if (window.Swal) Swal.fire('Gagal membuka', 'Pratinjau tidak dapat dibuka. Perbarui browser atau muat ulang halaman.', 'error');
            return;
        }

        const modal = ensurePreviewModal();
        const pdfBlock = modal.querySelector('[data-esuket-pdf-block]');
        const frame = modal.querySelector('[data-esuket-pdf]');
        const gallery = modal.querySelector('[data-esuket-gallery]');
        const seq = ++requestSerial;

        modal.querySelector('[data-esuket-title]').textContent = onlyGallery ? 'Bukti Pendukung' : 'Preview Surat';
        pdfBlock.hidden = !!onlyGallery;
        frame.removeAttribute('src');
        if (!onlyGallery && pdfUrl) frame.src = pdfUrl;
        gallery.innerHTML = '<div class="text-muted small"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat Bukti Pendukung...</div>';
        if (!modal.open) modal.showModal();

        try {
            const target = new URL(galleryUrl, window.location.href);
            if (target.origin !== window.location.origin) throw new Error('URL bukti tidak diizinkan');
            target.searchParams.set('fragment', '1');
            const response = await fetch(target.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            });
            if (!response.ok || response.redirected) throw new Error('Tidak dapat mengambil Bukti Pendukung (HTTP ' + response.status + ')');
            const markup = await response.text();
            if (seq !== requestSerial) return;
            gallery.innerHTML = markup;
        } catch (error) {
            if (seq !== requestSerial) return;
            gallery.replaceChildren();
            const message = document.createElement('div');
            message.className = 'small text-danger';
            message.textContent = 'Bukti Pendukung tidak dapat dimuat. Periksa akses dan koneksi, lalu coba kembali.';
            gallery.appendChild(message);
        }
    }

    // Bisa dipakai oleh icon mata di semua daftar dan oleh tombol di Beranda.
    window.esuketOpenSuratPreviewBukti = showPreviewBukti;

    $(document).on('click', '.js-esuket-preview', function (event) {
        event.preventDefault();
        showPreviewBukti(this.dataset.url, this.dataset.buktiUrl, false);
    });

    $(document).on('click', '.js-esuket-bukti-only', function (event) {
        event.preventDefault();
        showPreviewBukti(null, this.dataset.buktiUrl, true);
    });

    // Dialog pembesar terpisah menggunakan native <dialog>, bukan SweetAlert2.
    // Ketika Preview surat masih terbuka (showModal), SweetAlert2 dapat berada di
    // belakang top layer sehingga gambar terlihat tertutup oleh PDF/Preview.
    function ensureBuktiLightbox() {
        let element = document.getElementById('esuketBuktiLightboxModal');
        if (element) return element;

        element = document.createElement('dialog');
        element.id = 'esuketBuktiLightboxModal';
        element.setAttribute('aria-label', 'Perbesar Bukti Pendukung');
        element.style.cssText = 'width:min(1040px,96vw);max-width:96vw;max-height:94vh;border:0;border-radius:14px;padding:0;box-shadow:0 20px 70px rgba(0,0,0,.35);overflow:hidden;';
        element.innerHTML = `
            <div style="display:flex;flex-direction:column;max-height:94vh;background:#fff;color:#212529;">
                <div class="d-flex justify-content-between align-items-center gap-3 border-bottom px-3 px-md-4 py-3 bg-white">
                    <div class="fw-semibold text-truncate" data-esuket-bukti-title>Bukti Pendukung</div>
                    <button type="button" class="btn-close flex-shrink-0" data-esuket-bukti-close aria-label="Tutup gambar dan kembali ke Preview"></button>
                </div>
                <div data-esuket-bukti-content class="d-flex align-items-center justify-content-center p-2 p-md-3"
                     style="min-height:180px;max-height:calc(94vh - 65px);overflow:auto;background:#0f172a;"></div>
            </div>`;
        document.body.appendChild(element);
        element.querySelector('[data-esuket-bukti-close]').addEventListener('click', () => element.close());
        element.addEventListener('click', function (event) {
            if (event.target === element) element.close();
        });
        element.addEventListener('close', function () {
            // Lepaskan image/iframe agar loading PDF berhenti, Preview surat tetap hidup.
            element.querySelector('[data-esuket-bukti-content]').replaceChildren();
        });
        if (!document.getElementById('esuketBuktiLightboxStyle')) {
            const style = document.createElement('style');
            style.id = 'esuketBuktiLightboxStyle';
            style.textContent = '#esuketBuktiLightboxModal::backdrop{background:rgba(15,23,42,.8)}';
            document.head.appendChild(style);
        }
        return element;
    }

    $(document).on('click', '.js-esuket-bukti-file', function (event) {
        event.preventDefault();
        const url = this.dataset.fileUrl;
        const title = this.dataset.fileTitle || 'Bukti Pendukung';
        if (!url || typeof HTMLDialogElement === 'undefined') return;

        const lightbox = ensureBuktiLightbox();
        const content = lightbox.querySelector('[data-esuket-bukti-content]');
        lightbox.querySelector('[data-esuket-bukti-title]').textContent = title;
        content.replaceChildren();

        if (this.dataset.fileKind === 'pdf') {
            const pdf = document.createElement('iframe');
            pdf.src = url;
            pdf.title = title;
            pdf.style.cssText = 'display:block;width:100%;height:75vh;min-height:260px;border:0;border-radius:8px;background:#fff;';
            content.appendChild(pdf);
        } else {
            const img = document.createElement('img');
            img.src = url;
            img.alt = title;
            img.style.cssText = 'display:block;max-width:100%;max-height:calc(94vh - 100px);width:auto;height:auto;object-fit:contain;border-radius:6px;';
            content.appendChild(img);
        }

        // showModal() menempatkan pembesar di top layer DI ATAS dialog Preview,
        // tanpa menutup Preview di belakangnya. X / Esc mengembalikan ke Preview.
        if (!lightbox.open) lightbox.showModal();
    });
})();

$(document).on("click", ".js-lihat-catatan", function (e) {
    e.preventDefault();
    Swal.fire({ title: "Catatan Penurunan Surat", text: $(this).attr("data-catatan") || "-", icon: "info", confirmButtonText: "Tutup" });
});

$(document).on("click", ".js-surat-action", function (e) {
    e.preventDefault();

    const btn = $(this);
    const url = btn.data("url");
    const method = btn.data("method") || "POST";
    const action = btn.data("action");
    const confirmText = btn.data("confirm") || "Apakah Anda yakin?";
    const successText = btn.data("success") || "Berhasil.";
    const promptText = btn.data("prompt") || null;
    const promptRequired = String(btn.data("promptRequired") || "0") === "1";

    if (action === "preview") {
        // Hanya preview dengan atribut Bukti Pendukung yang memakai modal terintegrasi.
        // Endpoint PDF tetap sama, tanpa penyisipan gambar ke dokumen bertanda tangan.
        if (btn.data("buktiUrl") && window.esuketOpenSuratPreviewBukti) {
            window.esuketOpenSuratPreviewBukti(url, btn.data("buktiUrl"), false);
            return;
        }
        const w = 600;
        const h = 1000;
        const left = (screen.width / 2) - (w / 2);
        const top = (screen.height / 2) - (h / 2);

        window.open(
            url,
            "preview",
            `width=${w},height=${h},top=${top},left=${left},resizable=yes,scrollbars=yes`
        );
        return;
    }

    if (action === "preview_basah") {
        Swal.fire({
            title: "TTD Basah",
            text: confirmText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Lanjutkan",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (!result.isConfirmed) return;

            const w = 600;
            const h = 1000;
            const left = (screen.width / 2) - (w / 2);
            const top = (screen.height / 2) - (h / 2);

            window.open(
                url,
                "preview_basah",
                `width=${w},height=${h},top=${top},left=${left},resizable=yes,scrollbars=yes`
            );

            if ($.fn.DataTable && $("#tableSurat").length) {
                setTimeout(function () {
                    $("#tableSurat").DataTable().ajax.reload(null, false);
                }, 1200);
            }
        });
        return;
    }

    if (action === "upload_bukti_basah") {
        Swal.fire({
            title: "Upload Bukti TTD Basah",
            text: "Upload foto JPG/PNG dari surat fisik yang sudah ditandatangani. Sistem akan membuat PDF final secara otomatis.",
            icon: "info",
            input: "file",
            inputAttributes: {
                accept: ".jpg,.jpeg,.png,image/jpeg,image/png",
                "aria-label": "Upload bukti TTD Basah"
            },
            showCancelButton: true,
            confirmButtonText: "Upload",
            cancelButtonText: "Batal",
            preConfirm: (file) => {
                if (!file) {
                    Swal.showValidationMessage("Foto TTD Basah wajib dipilih");
                    return false;
                }
                if (!["image/jpeg", "image/png"].includes(file.type) || !/\.(jpe?g|png)$/i.test(file.name)) {
                    Swal.showValidationMessage("Pilih foto JPG, JPEG, atau PNG. PDF tidak diterima untuk upload baru.");
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    Swal.showValidationMessage("Ukuran foto maksimal 5 MB.");
                    return false;
                }
                return file;
            }
        }).then((result) => {
            if (!result.isConfirmed || !result.value) return;

            const formData = new FormData();
            formData.append("bukti_ttd_basah", result.value);

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (response) {
                    Toastify({
                        text: response.message || successText,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        style: {
                            background: "rgba(25, 135, 84, 1)",
                        },
                    }).showToast();

                    if ($.fn.DataTable && $("#tableSurat").length) {
                        $("#tableSurat").DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    let msg = "Terjadi kesalahan saat upload bukti TTD Basah";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: "Gagal",
                        text: msg,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
        return;
    }

    const swalConfig = {
        title: "Konfirmasi",
        text: confirmText,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal"
    };

    const needsNote = action === "turunkan";
    if (needsNote || promptText) {
        swalConfig.inputLabel = needsNote ? "Catatan perbaikan (wajib)" : promptText;
        swalConfig.input = "textarea";
        swalConfig.inputPlaceholder = needsNote ? "Jelaskan dokumen/kelengkapan yang perlu diperbaiki..." : "Tulis alasan penolakan...";
        swalConfig.inputAttributes = { autocapitalize: "off" };
        swalConfig.preConfirm = (value) => {
            if ((promptRequired || needsNote) && (!value || String(value).trim().length < (needsNote ? 5 : 1))) {
                Swal.showValidationMessage(needsNote ? "Catatan penurunan wajib diisi minimal 5 karakter" : "Alasan penolakan wajib diisi");
            }
            return value;
        };
    }

    Swal.fire(swalConfig).then((result) => {
        if (!result.isConfirmed) return;

        const payload = {};
        if (needsNote || promptText) {
            payload.komentar = String(result.value || "").trim();
        }

        $.ajax({
            url: url,
            method: method,
            data: payload,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function (response) {
                Toastify({
                    text: response.message || successText,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: "rgba(25, 135, 84, 1)",
                    },
                }).showToast();

                if ($.fn.DataTable && $("#tableSurat").length) {
                    $("#tableSurat").DataTable().ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                let msg = "Terjadi kesalahan";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: "Gagal",
                    text: msg,
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });
});

// Revisi lampiran SKTM setelah TTE Lurah: hanya menambah bukti; PDF TTE tidak diubah.
// Lampiran SKTM yang diturunkan berjenjang: diunggah Admin Kelurahan saja.
// Memakai input HTML multiple karena SweetAlert input=file tunggal bisa mengembalikan
// File, bukan FileList pada versi SweetAlert2 tertentu.
$(document).on('click', '.js-tambah-bukti-sktm', function (e) {
    e.preventDefault();
    const btn = $(this);
    const url = btn.attr('data-url');
    if (!url || !window.Swal) return;
    Swal.fire({
        title: 'Lengkapi Bukti Pendukung SKTM',
        text: 'File lama tidak dihapus. PDF yang sudah TTE Lurah tetap dipertahankan.',
        html: '<input id="revisi-bukti-sktm-files" type="file" multiple accept=".jpg,.jpeg,.png,.webp,.bmp,.pdf" class="form-control" aria-label="Pilih Bukti Pendukung" />',
        showCancelButton: true,
        confirmButtonText: 'Simpan Bukti',
        cancelButtonText: 'Batal',
        preConfirm: function () {
            const input = document.getElementById('revisi-bukti-sktm-files');
            const list = input ? Array.from(input.files || []) : [];
            if (!list.length || list.length > 20 || list.some(f => f.size > 5*1024*1024)) {
                Swal.showValidationMessage('Pilih 1–20 file JPG, PNG, WEBP, BMP atau PDF (maksimal 5 MB per file).');
                return false;
            }
            return list;
        }
    }).then(result => {
        if (!result.isConfirmed || !Array.isArray(result.value)) return;
        const form = new FormData();
        result.value.forEach(file => form.append('bukti_pendukung[]', file));
        btn.prop('disabled', true);
        $.ajax({
            url: url, type: 'POST', data: form, processData: false, contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                Swal.fire('Tersimpan', res.message || 'Bukti berhasil ditambahkan.', 'success');
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableSurat')) {
                    $('#tableSurat').DataTable().ajax.reload(null, false);
                }
                if (typeof window.refreshSuratDikembalikanNotif === 'function') {
                    window.refreshSuratDikembalikanNotif();
                }
            },
            error: function (xhr) {
                Swal.fire('Gagal', xhr.responseJSON?.message || 'Gagal mengunggah bukti.', 'error');
            },
            complete: function () { btn.prop('disabled', false); }
        });
    });
});

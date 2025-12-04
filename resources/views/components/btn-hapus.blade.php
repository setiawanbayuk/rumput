@props([
    // id unik modal
    'modalId' => 'modalHapus',

    // UI text
    'title' => 'Anda yakin ingin membatalkan dan menghapus pengajuan surat?',
    'message' =>
        'Tindakan ini akan menghapus data pengajuan Anda. Surat ini tidak akan diproses dan akan hilang dari riwayat pengajuan Anda.',
    'cancelText' => 'Batal',
    'confirmText' => 'Hapus',

    // Form target yang akan disubmit
    // WAJIB: id form di halaman pemanggil (bukan di komponen ini)
    'formId',

    // Nama input hidden untuk menampung ID pengajuan di form target
    'hiddenName' => 'id',
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header text-center"
                style="background:#7896B2; color:#fff; border-top-left-radius:18px; border-top-right-radius:18px;">
                <h5 class="modal-title fw-semibold mx-auto text-center" id="{{ $modalId }}Label">
                    {{ $title }}</h5>
            </div>

            <div class="modal-body text-center">
                <img src="{{ asset('assets/images/hapus-surat.png') }}" alt="Hapus" style="max-width:200px"
                    class="mb-3 items-center">
                <p class="mb-2 mx-2 text-start">{{ $message }}</p>
            </div>

            <div class="modal-footer justify-content-center gap-3">
                <button type="button" class="btn text-white px-4" id="{{ $modalId }}_submit"
                    style="border-radius:999px; background:#dc3545;">
                    {{ $confirmText }}
                </button>
                <button type="button" class="btn text-white px-4" data-bs-dismiss="modal"
                    style="border-radius:999px; background:#AEA07A;">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.bootstrap) return;

            const modalEl = document.getElementById(@json($modalId));
            const modal = new bootstrap.Modal(modalEl);
            const submit = document.getElementById(@json($modalId . '_submit'));
            const formId = @json($formId);
            const hiddenName = @json($hiddenName);

            // ambil id dari tombol pemicu
            modalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                const id = button.getAttribute('data-id');
                const form = document.getElementById(formId);
                if (!form) return;

                let hid = form.querySelector(`input[name="${hiddenName}"]`);
                if (!hid) {
                    hid = document.createElement('input');
                    hid.type = 'hidden';
                    hid.name = hiddenName;
                    form.appendChild(hid);
                }
                hid.value = id;
                form.dataset.deleteId = id;
            });

            // klik tombol hapus
            submit.addEventListener('click', async function() {
                const form = document.getElementById(formId);
                if (!form) return;

                const tpl = form.getAttribute('data-action-template');
                const id = form.dataset.deleteId || '';
                if (tpl && id) form.action = tpl.replace('__ID__', id);

                const original = submit.innerHTML;
                submit.disabled = true;
                submit.innerHTML = 'Memproses...';

                try {
                    const res = await fetch(form.action, {
                        method: (form.method || 'POST').toUpperCase(),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                .content,
                            'Accept': 'application/json',
                        },
                        body: new FormData(form)
                    });
                    const json = await res.json();

                    if (res.ok) {
                        alert(json.message || 'Berhasil dihapus.');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        location.reload();
                    } else {
                        alert(json.message || 'Terjadi kesalahan.');
                    }
                } catch (err) {
                    alert('Koneksi gagal. Silakan coba lagi.');
                } finally {
                    submit.disabled = false;
                    submit.innerHTML = original;
                }
            });
        });
    </script>
@endpushOnce

{{-- Modal Hapus SKBN --}}
<div class="modal fade" id="modalHapusSkbn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header" style="background:#7896B2; color:#fff;">
                <h6 class="modal-title m-0">Anda yakin ingin membatalkan dan menghapus pengajuan surat?</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('assets/images/hapus-surat.png') }}" alt="Hapus" style="max-width:240px" class="mb-3">
                <form id="formHapusSkbn" data-action-template="{{ route('skbn.hapus', ['id' => '__ID__']) }}">
                    @csrf
                    <input type="hidden" id="hapusSkbnId">
                    <div class="d-flex justify-content-center gap-3 m-2">
                        <button type="submit" class="btn"
                            style="background:#B4554F; color:#fff; border-radius:999px; padding:.5rem 1.4rem;">Iya</button>
                        <button type="button" class="btn" data-bs-dismiss="modal"
                            style="background:#AEA07A; color:#fff; border-radius:999px; padding:.5rem 1.4rem;">Tidak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('modalHapusSkbn');
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('formHapusSkbn');
            const idInp = document.getElementById('hapusSkbnId');

            // buka modal
            document.querySelectorAll('.btn-open-hapus-skbn').forEach(btn => {
                btn.addEventListener('click', () => {
                    idInp.value = btn.dataset.id;
                    modal.show();
                });
            });

            // submit (versi JSON biasa)
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = idInp.value;
                const url = form.dataset.actionTemplate.replace('__ID__', id);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                });
                const json = await res.json();
                if (res.ok) {
                    alert(json.message);
                    location.reload();
                } else {
                    alert(json.message || 'Terjadi kesalahan.');
                }
            });
        });
    </script>
@endpush

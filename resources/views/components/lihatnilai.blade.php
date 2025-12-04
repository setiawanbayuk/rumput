{{-- MODAL LIHAT PENILAIAN --}}
<div class="modal fade" id="modalLihatNilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            
            <div class="modal-header" style="background:#AEA07A; color:#fff;">
                <h5 class="modal-title fw-semibold mx-auto text-center">
                    Penilaian Anda
                </h5>
            </div>

            <div class="modal-body">
                <div class="text-center mb-3">
                    <div id="lihatStars" style="font-size:32px; color:#e2b84c;">
                        {{-- Stars filled by JS --}}
                    </div>
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Komentar:</label>
                    <p id="lihatKomentar" class="text-muted mb-0">—</p>
                </div>

                <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                    <i class="ri-time-line"></i> 
                    Dinilai pada: <span id="lihatTanggalNilai">—</span>
                </div>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn text-white px-4" data-bs-dismiss="modal" 
                    style="border-radius:999px; background:#AEA07A;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

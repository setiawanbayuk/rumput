<div class="modal fade" id="modalNilai" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 p-3">

            <div class="modal-header border-0">
                <h5 class="fw-bold">Nilai Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="fw-bold mb-1" id="judulSurat"></p>
                <small>No. Surat : <span id="noSurat"></span></small>

                <div class="mt-3">
                    <div class="rating-star">
                        <i class="ri-star-fill" data-value="1"></i>
                        <i class="ri-star-fill" data-value="2"></i>
                        <i class="ri-star-fill" data-value="3"></i>
                        <i class="ri-star-fill" data-value="4"></i>
                        <i class="ri-star-fill" data-value="5"></i>
                    </div>
                </div>

                <textarea id="komentar"
                        class="form-control p-3 mt-3"
                        style="border-radius:15px; min-height:140px; border:1px solid #d5c29a;"
                        placeholder="Berikan pendapat anda mengenai pengajuan surat ini..."></textarea>
            </div>

            <div class="modal-footer border-0 justify-content-end">
                <button class="btn btn-chip" style="border-color:#AEA07A; color:#AEA07A" data-bs-dismiss="modal">Nanti Saja</button>
                <button class="btn btn-chip text-white" style="background:#AEA07A" id="btnKirimNilai">OK</button>
            </div>
        </div>
    </div>
</div>

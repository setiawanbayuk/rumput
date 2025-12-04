<div class="modal fade" id="modalPassword" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">

            <div class="modal-header position-relative text-center"
                style="background:#7896B2; color:#fff; border-top-left-radius:18px; border-top-right-radius:18px;">
                <h5 class="modal-title fw-semibold mx-auto" id="modalPasswordLabel">Edit Password</h5>

                <!-- Tombol X (close) -->
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" style="box-shadow: none;" 
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('profile.akun', Auth::id()) }}">
                    @csrf
                    @method('PUT')

                    {{-- Input Password Baru --}}
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="password" name="password" id="password" class="form-control"
                                style="border: 1px solid #AEA07A; border-radius: 8px;" placeholder="">
                            <label for="password">New Password <span class="text-danger">*</span></label>
                        </div>
                        <ul class="mt-2 small text-muted">
                            <li>Panjang minimal 8 karakter</li>
                            <li>Harus mengandung campuran huruf besar dan kecil (A-a)</li>
                            <li>Harus menyertakan angka (1, 2, 3, dst.)</li>
                            <li>Harus menyertakan simbol (!, @, #, $, %, dst.)</li>
                        </ul>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="password" name="password_confirmation" class="form-control"
                                style="border: 1px solid #AEA07A; border-radius: 8px;" placeholder="">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-white py-2 px-4"
                            style="background: #7896B2; border-radius: 8px;">
                            <i class="ri-save-3-line me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

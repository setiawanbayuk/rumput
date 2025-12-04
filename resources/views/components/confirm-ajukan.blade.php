@props([
    'modalId' => 'modal',
    'title' => 'Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?',
    'message' =>
        'Pastikan isian ajuan telah sesuai. Kesalahan pengisian data atau lampiran dokumen dapat mengakibatkan ajuan ditolak saat proses verifikasi.',
    'agreeLabel' => 'Saya menyatakan bahwa isian formulir dan dokumen yang terlampir pada ajuan ini telah sesuai',
    'cancelText' => 'Periksa Kembali',
    'confirmText' => 'Kirim Ajuan',
    'formId' => null,
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 18px;">
            <div class="modal-header text-center"
                style="background:#7896B2; color:#fff; border-top-left-radius:18px; border-top-right-radius:18px;">
                <h5 class="modal-title fw-semibold mx-auto" id="{{ $modalId }}Label">{{ $title }}</h5>
            </div>

            <div class="modal-body ms-2">
                <p class="mb-3">{{ $message }}</p>
                <label class="card p-2 shadow-sm d-block me-2">
                    <input type="checkbox" class="form-check-input me-2" style="border-color: #AEA07A" id="{{ $modalId }}_agree">
                    <span class="align-middle" style="color: #AEA07A">{{ $agreeLabel }}</span>
                </label>
            </div>

            <div class="modal-footer justify-content-center gap-3">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:999px;">
                    {{ $cancelText }}
                </button>

                {{-- Tombol submit form --}}
                <button type="button" class="btn text-white px-4" id="{{ $modalId }}_submit"
                    style="border-radius:999px; background:#b8a57f;" disabled>
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const agree = document.getElementById('{{ $modalId }}_agree');
            const submit = document.getElementById('{{ $modalId }}_submit');
            const formId = @json($formId);

            if (agree && submit) {
                agree.addEventListener('change', () => {
                    submit.disabled = !agree.checked;
                });

                submit.addEventListener('click', () => {
                    if (!agree.checked) return;
                    if (formId) {
                        const f = document.getElementById(formId);
                        if (f) {
                            f.submit();
                        }
                    }
                });
            }
        });
    </script>
@endpushOnce

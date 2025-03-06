<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Form :: Clients
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="registerForm" action="" method="post">
                    <input type="hidden" name="_id">

                    <div class="mb-3">
                        <label class="form-label">No Register <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="register" name="register" autocomplete=""
                            required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-subtle-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-subtle-primary px-5" id="btn-reg" name="btn-reg"
                    onclick="handleSubmitReg(this)">Register</button>
            </div>
        </div>
    </div>
</div>

<script>
    function handleSubmitReg(e) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Aapakah yakin akan mendaftarkan dokumen ini?!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
                let web = '{{ env('APP_URL') }}';
                var data = $('#registerForm').serialize();
                console.log(data);
                $.ajax({
                    type: 'POST',
                    url: web + '/api/register',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'aplication/json'
                    },
                    data: data,
                    beforeSend: function() {
                        Swal.fire({
                            html: `
                                <div>
                                    <div>Process</div>
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>`,
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });
                    },
                    success: function(response) {
                        Swal.fire(
                            response.message,
                            'Terima kasih!',
                            response.status).then(function() {
                            $('#registerModal').modal('hide')
                            // window.location.href = obj.url;
                        });
                        $('#tableSurat').DataTable().ajax.reload();
                    },
                    error: function() { // if error occured
                        Swal.fire(
                            'Registrasi Dokumen Gagal!',
                            'Mohon Maaf!',
                            'error').then(function() {
                            $('#registerModal').modal('hide')
                        });
                        $('#tableSurat').DataTable().ajax.reload();
                    },
                })
            }
        })
    }
</script>

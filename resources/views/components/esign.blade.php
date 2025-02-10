<div class="modal fade" id="esignModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Form :: Clients
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="clientsForm" action="" method="post">
                    <input type="hidden" name="_id">
                    <input type="hidden" name="jenis">

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="nik" name="nik"
                                placeholder="Masukkan 16 digit NIK" aria-label="NIK" aria-describedby="basic-addon2">
                            <button type="button" class="input-group-text btn btn-subtle-primary"
                                onclick="checkEsign()">Cek</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-center" id="status" style="font-weight: bold;font-size: 14px;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Passphrase <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="passphrase" name="passphrase" autocomplete=""
                            required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-subtle-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-subtle-primary px-5" id="btn-ttd" name="btn-ttd" disabled
                    onclick="handleSubmit(this)">Sign</button>
            </div>
        </div>
    </div>
</div>

<script>
    function checkEsign() {
        let nik = document.getElementById("nik").value;
        $("#status").html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
        );
        if (nik.length == 16) {
            let web = '{{ env('APP_URL') }}';
            document.getElementById('nik').innerHTML = nik.length + '/16';
            $.ajax({
                type: "GET",
                dataType: "json",
                url: web + '/api/esign/check/' + nik,
                success: function(response) {
                    $("#status").html(response.message);
                    if (response.status_code == '1111') {
                        $("#btn-ttd").removeAttr('disabled');
                    } else {
                        $('#btn-ttd').prop("disabled", true);
                    }
                }
            });
        } else {
            $("#status").html('<span style="color:red">NIK Tidak Valid</span>');
            document.getElementById('nik').innerHTML = nik.length + '/16';
        }
    }

    function handleSubmit(e) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Aapakah yakin akan membubuhkan TTE pada dokumen ini?!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
                let web = '{{ env('APP_URL') }}';
                var data = $('#clientsForm').serialize();
                $.ajax({
                    type: 'POST',
                    url: web + '/api/esign/sign',
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
                            $('#esignModal').modal('hide')
                            // window.location.href = obj.url;
                        });
                        $('#tableSurat').DataTable().ajax.reload();
                    },
                    error: function() { // if error occured
                        Swal.fire(
                            'Tanda Tangan Dokumen Gagal!',
                            'Mohon Maaf!',
                            'error').then(function() {
                            $('#esignModal').modal('hide')
                        });
                        $('#tableSurat').DataTable().ajax.reload();
                    },
                })
            }
        })
    }
</script>

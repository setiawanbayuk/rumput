<!-- Letakkan modal ini di paling bawah <body> -->
<div class="modal fade" id="esignModal" tabindex="-1" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Form :: Clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="clientsForm" method="post">
                    <input type="hidden" name="_id">
                    <input type="hidden" name="jenis">
                    <input type="hidden" name="role">

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan 16 digit NIK">
                            <button type="button" class="input-group-text btn btn-subtle-primary" onclick="checkEsign()">Cek</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-center fw-bold" id="status" style="font-size:14px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Passphrase <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="passphrase" name="passphrase" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-subtle-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-subtle-primary px-5" id="btn-ttd" disabled onclick="handleSubmit(this)">
                    Sign
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    function checkEsign() {
        let nik = $("#nik").val();
        let web = '{{ env("APP_URL") }}';

        if (nik.length !== 16) {
            $("#status").html('<span class="text-danger">NIK Tidak Valid</span>');
            $("#btn-ttd").prop("disabled", true);
            return;
        }

        $("#status").html(`
            <span class="spinner-border spinner-border-sm"></span> Loading...
        `);

        $.ajax({
            type: "GET",
            url: `${web}/api/esign/check/${nik}`,
            dataType: "json",

            success: function (res) {
                $("#status").html(res.message);

                if (res.status_code == '1111') {
                    $("#btn-ttd").removeAttr('disabled');
                } else {
                    $("#btn-ttd").prop("disabled", true);
                }
            }
        });
    }


    function handleSubmit(btn) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Apakah yakin akan membubuhkan TTE pada dokumen ini?!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (!result.isConfirmed) return;

            let web = '{{ env("APP_URL") }}';
            let data = $('#clientsForm').serialize();

            $.ajax({
                type: 'POST',
                url: `${web}/api/esign/sign`,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: data,

                beforeSend: function () {
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

                success: function (res) {

                    Swal.fire(res.message, 'Terima kasih!', res.status)
                        .then(() => {
                            $('#esignModal').modal('hide');
                        });

                    $('#tableSurat').DataTable().ajax.reload();
                },

                error: function () {
                    Swal.fire(
                        'Tanda Tangan Dokumen Gagal!',
                        'Mohon Maaf!',
                        'error'
                    ).then(() => {
                        $('#esignModal').modal('hide');
                    });

                    $('#tableSurat').DataTable().ajax.reload();
                },
            });
        });
    }
</script>

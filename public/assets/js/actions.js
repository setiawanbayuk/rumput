$(document).on("click", ".js-surat-action", function (e) {
    e.preventDefault();

    const btn = $(this);
    const url = btn.data("url");
    const method = btn.data("method") || "POST";
    const action = btn.data("action"); // <-- penting
    const confirmText = btn.data("confirm") || "Apakah Anda yakin?";
    const successText = btn.data("success") || "Berhasil.";

    // === KHUSUS UNTUK PREVIEW ===
    if (action === "preview") {

        const w = 600;
        const h = 1000;

        const left = (screen.width / 2) - (w / 2);
        const top = (screen.height / 2) - (h / 2);

        window.open(
            url,
            "preview",
            `width=${w},height=${h},top=${top},left=${left},resizable=yes,scrollbars=yes`
        );

        return; // STOP di sini, tidak lanjut ke ajax
    }

    // === AKSI NORMAL (PROSES, TOLAK, SETUJUI, DLL) ===
    Swal.fire({
        title: "Konfirmasi",
        text: confirmText,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (!result.value) return;

        $.ajax({
            url: url,
            method: method,
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

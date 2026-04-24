$(document).on("click", ".js-surat-action", function (e) {
    e.preventDefault();

    const btn = $(this);
    const url = btn.data("url");
    const method = btn.data("method") || "POST";
    const action = btn.data("action");
    const confirmText = btn.data("confirm") || "Apakah Anda yakin?";
    const successText = btn.data("success") || "Berhasil.";
    const promptText = btn.data("prompt") || null;
    const promptRequired = String(btn.data("promptRequired") || "0") === "1";

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
        return;
    }

    if (action === "preview_basah") {
        Swal.fire({
            title: "TTD Basah",
            text: confirmText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Lanjutkan",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (!result.isConfirmed) return;

            const w = 600;
            const h = 1000;
            const left = (screen.width / 2) - (w / 2);
            const top = (screen.height / 2) - (h / 2);

            window.open(
                url,
                "preview_basah",
                `width=${w},height=${h},top=${top},left=${left},resizable=yes,scrollbars=yes`
            );

            if ($.fn.DataTable && $("#tableSurat").length) {
                setTimeout(function () {
                    $("#tableSurat").DataTable().ajax.reload(null, false);
                }, 1200);
            }
        });
        return;
    }

    const swalConfig = {
        title: "Konfirmasi",
        text: confirmText,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal"
    };

    if (promptText) {
        swalConfig.input = "textarea";
        swalConfig.inputLabel = promptText;
        swalConfig.inputPlaceholder = "Tulis alasan penolakan...";
        swalConfig.inputAttributes = { autocapitalize: "off" };
        swalConfig.preConfirm = (value) => {
            if (promptRequired && (!value || !String(value).trim())) {
                Swal.showValidationMessage("Alasan penolakan wajib diisi");
            }
            return value;
        };
    }

    Swal.fire(swalConfig).then((result) => {
        if (!result.value && !(promptText && result.isConfirmed)) return;

        const payload = {};
        if (promptText) {
            payload.komentar = result.value;
        }

        $.ajax({
            url: url,
            method: method,
            data: payload,
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

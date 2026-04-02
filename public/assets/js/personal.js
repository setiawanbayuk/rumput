function checkNIK() {
    let nik = document.getElementById("nik").value;

    // Validasi sederhana sebelum kirim request
    if (nik.length < 16) {
        Swal.fire("Peringatan", "NIK harus 16 digit!", "warning");
        return;
    }

    $.ajax({
        url: window.location.origin + "/api/personal?nik=" + nik,
        type: "GET",
        success: function (response) {
            // PENYESUAIAN: Karena respon API baru adalah { status: 'success', data: {...} }
            // Maka kita ambil object di dalam response.data
            const res = response.data;

            $("#kk").val(res.kk);
            $("#name").val(res.name);
            $("#tempat_lhr").val(res.tempat_lhr);
            $("#tgl_lhr").val(res.tgl_lhr);
            $("#alamat").val(res.alamat);

            // Fungsi pembantu agar kode lebih bersih (DRY)
            const updateSelect2 = (id, value, text) => {
                if ($(id).length) {
                    $(id).select2("trigger", "select", {
                        data: { id: value, text: text },
                    });
                }
            };

            updateSelect2("#gender", res.gender, res.gender_nm);
            updateSelect2("#status_kwn", res.status_kwn, res.status_kwn_nm);
            updateSelect2("#kewarganegaraan", res.kewarganegaraan, res.kewarganegaraan_nm);
            updateSelect2("#agama", res.agama, res.agama_nm);
            updateSelect2("#pendidikan", res.pendidikan, res.pendidikan_nm);
            updateSelect2("#pekerjaan", res.pekerjaan, res.pekerjaan_nm);
            updateSelect2("#provinsi", res.provinsi, res.provinsi_nm);
            updateSelect2("#kabko", res.kabko, res.kabko_nm);
            updateSelect2("#kecamatan", res.kecamatan, res.kecamatan_nm);
            updateSelect2("#kelurahan", res.kelurahan, res.kelurahan_nm);
            updateSelect2("#rw", res.rw, res.rw_nm);
            updateSelect2("#rt", res.rt, res.rt_nm);

            Toastify({
                text: "Data ditemukan!",
                duration: 2000,
                close: true,
                gravity: "top",
                position: "center",
                style: {
                    background: "rgba(25, 135, 84, 1)",
                },
            }).showToast();
        },
        error: function (xhr) {
            let message = "Terjadi kesalahan sistem";
            try {
                const response = JSON.parse(xhr.responseText);
                message = response.message;
            } catch (e) {}

            Swal.fire({
                title: "Ooopppsss...",
                text: message,
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

$(function () {
    let provinsi_id = "";
    let kabko_id = "";
    let kecamatan_id = "";
    let kelurahan_id = "";
    let rw_id ="";
    $("#gender").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("gender.index"),
            dataType: "json",
            processResults: function (response) {
                return {
                    results: response,
                };
            },
        },
    });
    $("#pekerjaan").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("pekerjaan.index"),
            dataType: "json",
            data: (params) => {
                let query = {
                    q: params.term,
                    page: params.page || 1,
                };
                return query;
            },
            processResults: function (response) {
                return {
                    results: response,
                    pagination: {
                        more: response.current_page < response.last_page,
                    },
                };
            },
        },
    });
    $("#pendidikan").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("pendidikan.index"),
            dataType: "json",
            data: (params) => {
                let query = {
                    q: params.term,
                    page: params.page || 1,
                };
                return query;
            },
            processResults: function (response) {
                console.log("bawah");
                return {
                    results: response,
                    pagination: {
                        more: response.current_page < response.last_page,
                    },
                };
            },
        },
    });
    $("#agama").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("agama.index"),
            dataType: "json",
            processResults: function (response) {
                return {
                    results: response,
                };
            },
        },
    });

    $("#kewarganegaraan").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("kewarganegaraan.index"),
            dataType: "json",
            processResults: function (response) {
                return {
                    results: response,
                };
            },
        },
    });

    $("#status_kwn").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("status_kwn.index"),
            dataType: "json",
            processResults: function (response) {
                return {
                    results: response,
                };
            },
        },
    });
    $("#provinsi").select2({
        theme: "bootstrap-5",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $(this).data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("provinsi.index"),
            dataType: "json",
            processResults: function (response) {
                return {
                    results: response,
                };
            },
        },
    });
    $("#provinsi").on("change", function () {
        provinsi_id = $(this).val();
        $("#kabko").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url:
                    window.location.origin + "/api/kabko?kode_provinsi=" + provinsi_id,
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });
    $("#kabko").on("change", function () {
        kabko_id = $(this).val();
        $("#kecamatan").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url:
                    window.location.origin + "/api/kecamatan?kode_kabkota=" + kabko_id, //route('regional.kecamatan'),
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });

    $("#kecamatan").on("change", function () {
        kecamatan_id = $(this).val();
        $("#kelurahan").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url:
                    window.location.origin + "/api/kelurahan?kode_kecamatan=" +
                    kecamatan_id, //route('regional.kelurahan'),
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });
    // KELURAHAN -> RW
    $("#kelurahan").on("change", function () {
        kelurahan_id = $(this).val();
        $("#rw").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $(this).data("placeholder"),
            ajax: {
                url:
                    window.location.origin +
                    "/api/rw?kode_kelurahan=" +
                    kelurahan_id,
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });

    // RW -> RT
    $("#rw").on("change", function () {
        kelurahan_id = $("#kelurahan").val();
        rw_id = $(this).val();
        $("#rt").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $(this).data("placeholder"),
            ajax: {
                url:
                    window.location.origin +
                    "/api/rt?kode_kelurahan=" +
                    kelurahan_id +
                    "&rw=" +
                    rw_id,
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });

    // ---------- RESET CHAIN ----------
    function reset(id) {
        $(id).empty().trigger("change");
    }

    // PROVINSI → reset kabko, kecamatan, kelurahan, rw, rt
    $("#provinsi").on("change", function () {
        reset("#kabko");
        reset("#kecamatan");
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    // KAB/KOTA → reset kecamatan, kelurahan, rw, rt
    $("#kabko").on("change", function () {
        reset("#kecamatan");
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    // KECAMATAN → reset kelurahan, rw, rt
    $("#kecamatan").on("change", function () {
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    // KELURAHAN → reset rw, rt
    $("#kelurahan").on("change", function () {
        reset("#rw");
        reset("#rt");
    });

    // RW → reset rt
    $("#rw").on("change", function () {
        reset("#rt");
    });
});

function checkNIK() {
    let nik = document.getElementById("nik").value;
    console.log(nik);

    $.ajax({
        url: window.location.origin + "/api/personal?nik=" + nik,
        success: function (response) {
            $("#kk").val(response.kk);
            $("#name").val(response.name);
            $("#tempat_lhr").val(response.tempat_lhr);
            $("#tgl_lhr").val(response.tgl_lhr);
            $("#alamat").val(response.alamat);
            $("#gender").select2("trigger", "select", {
                data: {
                    id: response.gender,
                    text: response.gender_nm,
                },
            });
            $("#status_kwn").select2("trigger", "select", {
                data: {
                    id: response.status_kwn,
                    text: response.status_kwn_nm,
                },
            });
            $("#kewarganegaraan").select2("trigger", "select", {
                data: {
                    id: response.kewarganegaraan,
                    text: response.kewarganegaraan_nm,
                },
            });
            $("#agama").select2("trigger", "select", {
                data: {
                    id: response.agama,
                    text: response.agama_nm,
                },
            });
            $("#pendidikan").select2("trigger", "select", {
                data: {
                    id: response.pendidikan,
                    text: response.pendidikan_nm,
                },
            });
            $("#pekerjaan").select2("trigger", "select", {
                data: {
                    id: response.pekerjaan,
                    text: response.pekerjaan_nm,
                },
            });
            $("#provinsi").select2("trigger", "select", {
                data: {
                    id: response.provinsi,
                    text: response.provinsi_nm,
                },
            });
            $("#kabko").select2("trigger", "select", {
                data: {
                    id: response.kabko,
                    text: response.kabko_nm,
                },
            });
            $("#kecamatan").select2("trigger", "select", {
                data: {
                    id: response.kecamatan,
                    text: response.kecamatan_nm,
                },
            });
            $("#kelurahan").select2("trigger", "select", {
                data: {
                    id: response.kelurahan,
                    text: response.kelurahan_nm,
                },
            });
            $("#rw").select2("trigger", "select", {
                data: {
                    id: response.rw,
                    text: response.rw_nm,
                },
            });
            $("#rt").select2("trigger", "select", {
                data: {
                    id: response.rt,
                    text: response.rt_nm,
                },
            });

            Toastify({
                text: "Data ditemukan!",
                duration: 1000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "rgba(25, 135, 84, 1)",
                },
            }).showToast();
        },
        error: function (xhr) {
            const response = JSON.parse(xhr.responseText);
            // console.log('hey error', response.message);
            Swal.fire({
                title: "Ooopppsss...",
                text: response.message,
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

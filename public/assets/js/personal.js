function toUpperSafe(value) {
    return (value || "").toString().toUpperCase();
}

function enforceUppercaseFields() {
    ["name", "tempat_lhr", "alamat"].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("input", function () {
            this.value = toUpperSafe(this.value);
        });

        el.value = toUpperSafe(el.value);
    });
}

function checkNIK() {
    let nik = document.getElementById("nik").value;

    if (nik.length !== 16) {
        Swal.fire("Peringatan", "NIK harus tepat 16 digit!", "warning");
        return;
    }

    $.ajax({
        url: window.location.origin + "/api/personal?nik=" + nik,
        type: "GET",
        success: function (response) {
            const root = response?.data ?? response ?? {};
            const biodata = root?.data ?? root ?? {};

            const pick = (...values) =>
                values.find(v => v !== undefined && v !== null && v !== "");

            $("#nik").val(pick(root.nik, biodata.nik, nik) || "");
            $("#kk").val(pick(root.kk, biodata.kk) || "");
            $("#name").val(toUpperSafe(pick(root.name, root.nama, biodata.name, biodata.nama) || ""));
            $("#tempat_lhr").val(
                toUpperSafe(pick(root.tempat_lhr, root.tempat_lahir, biodata.tempat_lhr, biodata.tempat_lahir) || "")
            );
            $("#tgl_lhr").val(
                pick(root.tgl_lhr, root.tanggal_lahir, biodata.tgl_lhr, biodata.tanggal_lahir) || ""
            );
            $("#alamat").val(toUpperSafe(pick(root.alamat, biodata.alamat) || ""));

            const updateSelect2 = (id, value, text, hiddenId = null) => {
                if (!$(id).length) return;
                if (value === undefined || value === null || value === "") return;

                const label =
                    text !== undefined && text !== null && text !== ""
                        ? text
                        : "-- data ditemukan, label belum tersedia --";

                $(id).empty();

                const option = new Option(label, value, true, true);
                $(id).append(option).trigger("change");

                if (hiddenId && $(hiddenId).length) {
                    $(hiddenId).val(label);
                }
            };

            updateSelect2(
                "#gender",
                pick(root.gender, biodata.gender),
                pick(root.gender_nm, biodata.gender_nm, root.gender, biodata.gender),
                "#gender_nm"
            );

            updateSelect2(
                "#status_kwn",
                pick(root.status_kwn, biodata.status_kwn),
                pick(root.status_kwn_nm, biodata.status_kwn_nm, root.status_kwn, biodata.status_kwn),
                "#status_kwn_nm"
            );

            updateSelect2(
                "#kewarganegaraan",
                pick(root.kewarganegaraan, biodata.kewarganegaraan),
                pick(root.kewarganegaraan_nm, biodata.kewarganegaraan_nm, root.kewarganegaraan, biodata.kewarganegaraan),
                "#kewarganegaraan_nm"
            );

            updateSelect2(
                "#agama",
                pick(root.agama, biodata.agama),
                pick(root.agama_nm, biodata.agama_nm, root.agama, biodata.agama),
                "#agama_nm"
            );

            updateSelect2(
                "#pendidikan",
                pick(root.pendidikan, biodata.pendidikan),
                pick(root.pendidikan_nm, biodata.pendidikan_nm, root.pendidikan, biodata.pendidikan),
                "#pendidikan_nm"
            );

            updateSelect2(
                "#pekerjaan",
                pick(root.pekerjaan, biodata.pekerjaan),
                pick(root.pekerjaan_nm, biodata.pekerjaan_nm, root.pekerjaan, biodata.pekerjaan),
                "#pekerjaan_nm"
            );

            updateSelect2(
                "#provinsi",
                pick(root.provinsi, biodata.provinsi),
                pick(root.provinsi_nm, biodata.provinsi_nm, root.provinsi, biodata.provinsi),
                "#provinsi_nm"
            );

            updateSelect2(
                "#kabko",
                pick(root.kabko, biodata.kabko),
                pick(root.kabko_nm, biodata.kabko_nm, root.kabko, biodata.kabko),
                "#kabko_nm"
            );

            updateSelect2(
                "#kecamatan",
                pick(root.kecamatan, biodata.kecamatan),
                pick(root.kecamatan_nm, biodata.kecamatan_nm, root.kecamatan, biodata.kecamatan),
                "#kecamatan_nm"
            );

            updateSelect2(
                "#kelurahan",
                pick(root.kelurahan, biodata.kelurahan),
                pick(root.kelurahan_nm, biodata.kelurahan_nm, root.kelurahan, biodata.kelurahan),
                "#kelurahan_nm"
            );

            updateSelect2(
                "#rw",
                pick(root.rw, biodata.rw),
                pick(root.rw_nm, biodata.rw_nm, root.rw, biodata.rw),
                "#rw_nm"
            );

            updateSelect2(
                "#rt",
                pick(root.rt, biodata.rt),
                pick(root.rt_nm, biodata.rt_nm, root.rt, biodata.rt),
                "#rt_nm"
            );

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

            console.log("Response personal:", response);
            console.log("Root data:", root);
            console.log("Biodata:", biodata);
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

function syncSelectLabel(selectId, hiddenId) {
    $(document).on("change", selectId, function () {
        const text = $(this).find("option:selected").text() || "";
        if ($(hiddenId).length) {
            $(hiddenId).val(text);
        }
    });
}

$(function () {
    let provinsi_id = "";
    let kabko_id = "";
    let kecamatan_id = "";
    let kelurahan_id = "";
    let rw_id = "";

    enforceUppercaseFields();

    syncSelectLabel("#gender", "#gender_nm");
    syncSelectLabel("#status_kwn", "#status_kwn_nm");
    syncSelectLabel("#kewarganegaraan", "#kewarganegaraan_nm");
    syncSelectLabel("#agama", "#agama_nm");
    syncSelectLabel("#pendidikan", "#pendidikan_nm");
    syncSelectLabel("#pekerjaan", "#pekerjaan_nm");
    syncSelectLabel("#provinsi", "#provinsi_nm");
    syncSelectLabel("#kabko", "#kabko_nm");
    syncSelectLabel("#kecamatan", "#kecamatan_nm");
    syncSelectLabel("#kelurahan", "#kelurahan_nm");
    syncSelectLabel("#rw", "#rw_nm");
    syncSelectLabel("#rt", "#rt_nm");

    $("#gender").select2({
        theme: "bootstrap-5",
        width: $("#gender").data("width")
            ? $("#gender").data("width")
            : $("#gender").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#gender").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("gender.index"),
            dataType: "json",
            processResults: function (response) {
                return { results: response };
            },
        },
    });

    $("#pekerjaan").select2({
        theme: "bootstrap-5",
        width: $("#pekerjaan").data("width")
            ? $("#pekerjaan").data("width")
            : $("#pekerjaan").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#pekerjaan").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("pekerjaan.index"),
            dataType: "json",
            data: (params) => ({
                q: params.term,
                page: params.page || 1,
            }),
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
        width: $("#pendidikan").data("width")
            ? $("#pendidikan").data("width")
            : $("#pendidikan").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#pendidikan").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("pendidikan.index"),
            dataType: "json",
            data: (params) => ({
                q: params.term,
                page: params.page || 1,
            }),
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

    $("#agama").select2({
        theme: "bootstrap-5",
        width: $("#agama").data("width")
            ? $("#agama").data("width")
            : $("#agama").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#agama").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("agama.index"),
            dataType: "json",
            processResults: function (response) {
                return { results: response };
            },
        },
    });

    $("#kewarganegaraan").select2({
        theme: "bootstrap-5",
        width: $("#kewarganegaraan").data("width")
            ? $("#kewarganegaraan").data("width")
            : $("#kewarganegaraan").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#kewarganegaraan").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("kewarganegaraan.index"),
            dataType: "json",
            processResults: function (response) {
                return { results: response };
            },
        },
    });

    $("#status_kwn").select2({
        theme: "bootstrap-5",
        width: $("#status_kwn").data("width")
            ? $("#status_kwn").data("width")
            : $("#status_kwn").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#status_kwn").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("status_kwn.index"),
            dataType: "json",
            processResults: function (response) {
                return { results: response };
            },
        },
    });

    $("#provinsi").select2({
        theme: "bootstrap-5",
        width: $("#provinsi").data("width")
            ? $("#provinsi").data("width")
            : $("#provinsi").hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: $("#provinsi").data("placeholder"),
        minimumInputLength: 2,
        ajax: {
            url: route("provinsi.index"),
            dataType: "json",
            processResults: function (response) {
                return { results: response };
            },
        },
    });

    $("#provinsi").on("change", function () {
        provinsi_id = $(this).val();
        $("#kabko").select2({
            theme: "bootstrap-5",
            width: $("#kabko").data("width")
                ? $("#kabko").data("width")
                : $("#kabko").hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $("#kabko").data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url: window.location.origin + "/api/kabko?kode_provinsi=" + provinsi_id,
                dataType: "json",
                processResults: function (response) {
                    return { results: response };
                },
            },
        });
    });

    $("#kabko").on("change", function () {
        kabko_id = $(this).val();
        $("#kecamatan").select2({
            theme: "bootstrap-5",
            width: $("#kecamatan").data("width")
                ? $("#kecamatan").data("width")
                : $("#kecamatan").hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $("#kecamatan").data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url: window.location.origin + "/api/kecamatan?kode_kabkota=" + kabko_id,
                dataType: "json",
                processResults: function (response) {
                    return { results: response };
                },
            },
        });
    });

    $("#kecamatan").on("change", function () {
        kecamatan_id = $(this).val();
        $("#kelurahan").select2({
            theme: "bootstrap-5",
            width: $("#kelurahan").data("width")
                ? $("#kelurahan").data("width")
                : $("#kelurahan").hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $("#kelurahan").data("placeholder"),
            minimumInputLength: 2,
            ajax: {
                url: window.location.origin + "/api/kelurahan?kode_kecamatan=" + kecamatan_id,
                dataType: "json",
                processResults: function (response) {
                    return { results: response };
                },
            },
        });
    });

    $("#kelurahan").on("change", function () {
        kelurahan_id = $(this).val();
        $("#rw").select2({
            theme: "bootstrap-5",
            width: $("#rw").data("width")
                ? $("#rw").data("width")
                : $("#rw").hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $("#rw").data("placeholder"),
            ajax: {
                url: window.location.origin + "/api/rw?kode_kelurahan=" + kelurahan_id,
                dataType: "json",
                processResults: function (response) {
                    return { results: response };
                },
            },
        });
    });

    $("#rw").on("change", function () {
        kelurahan_id = $("#kelurahan").val();
        rw_id = $(this).val();
        $("#rt").select2({
            theme: "bootstrap-5",
            width: $("#rt").data("width")
                ? $("#rt").data("width")
                : $("#rt").hasClass("w-100")
                    ? "100%"
                    : "style",
            placeholder: $("#rt").data("placeholder"),
            ajax: {
                url: window.location.origin + "/api/rt?kode_kelurahan=" + kelurahan_id + "&rw=" + rw_id,
                dataType: "json",
                processResults: function (response) {
                    return { results: response };
                },
            },
        });
    });

    function reset(id) {
        $(id).empty().trigger("change");
    }

    $("#provinsi").on("change", function () {
        reset("#kabko");
        reset("#kecamatan");
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    $("#kabko").on("change", function () {
        reset("#kecamatan");
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    $("#kecamatan").on("change", function () {
        reset("#kelurahan");
        reset("#rw");
        reset("#rt");
    });

    $("#kelurahan").on("change", function () {
        reset("#rw");
        reset("#rt");
    });

    $("#rw").on("change", function () {
        reset("#rt");
    });
});

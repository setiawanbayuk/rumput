$(function () {
    let provinsi_id = "";
    let kabko_id = "";
    let kecamatan_id = "";
    $("#provinsi_boro").select2({
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
    $("#provinsi_boro").on("change", function () {
        provinsi_id = $(this).val();
        $("#kabko_boro").select2({
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
                    window.location.origin +
                    "/api/kabko?kode_provinsi=" +
                    provinsi_id,
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });
    $("#kabko_boro").on("change", function () {
        kabko_id = $(this).val();
        $("#kecamatan_boro").select2({
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
                    window.location.origin +
                    "/api/kecamatan?kode_kabkota=" +
                    kabko_id, //route('regional.kecamatan'),
                dataType: "json",
                processResults: function (response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });

    $("#kecamatan_boro").on("change", function () {
        kecamatan_id = $(this).val();
        $("#kelurahan_boro").select2({
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
                    window.location.origin +
                    "/api/kelurahan?kode_kecamatan=" +
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
    // ---------- RESET CHAIN ----------
    function reset(id) {
        $(id).empty().trigger("change");
    }

    // PROVINSI → reset kabko, kecamatan, kelurahan
    $("#provinsi_boro").on("change", function () {
        reset("#kabko_boro");
        reset("#kecamatan_boro");
        reset("#kelurahan_boro");
    });

    // KAB/KOTA → reset kecamatan, kelurahan
    $("#kabko_boro").on("change", function () {
        reset("#kecamatan_boro");
        reset("#kelurahan_boro");
    });

    // KECAMATAN → reset kelurahan
    $("#kecamatan_boro").on("change", function () {
        reset("#kelurahan_boro");
    });

    // Fungsi prefill sederhana
    function prefill(id, value, text) {
        if (!value) return;

        const option = new Option(text || value, value, true, true);
        $(id).append(option).trigger("change");
    }

    // Ambil data atribut cara lama (manual satu-satu)
    let prov        = $("#provinsi_boro").data("selected");
    let prov_nm     = $("#provinsi_boro").data("selected-text");
    let kabko       = $("#kabko_boro").data("selected");
    let kabko_nm    = $("#kabko_boro").data("selected-text");
    let kec         = $("#kecamatan_boro").data("selected");
    let kec_nm      = $("#kecamatan_boro").data("selected-text");
    let kel         = $("#kelurahan_boro").data("selected");
    let kel_nm      = $("#kelurahan_boro").data("selected-text");

    // Jalankan prefill satu-satu (tanpa timeout)
    prefill("#provinsi_boro",   prov, prov_nm);
    prefill("#kabko_boro",      kabko,  kabko_nm);
    prefill("#kecamatan_boro",  kec,  kec_nm);
    prefill("#kelurahan_boro",  kel,  kel_nm);
});

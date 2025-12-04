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
        minimumInpuLength: 2,
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
            minimumInpuLength: 2,
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
            minimumInpuLength: 2,
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
            minimumInpuLength: 2,
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
    function resetSelect(id) {
        $(id).empty().trigger("change");
    }

    // PROVINSI → reset kabko, kecamatan, kelurahan, rw, rt
    $("#provinsi").on("change", function () {
        resetSelect("#kabko");
        resetSelect("#kecamatan");
        resetSelect("#kelurahan");
        resetSelect("#rw");
        resetSelect("#rt");
    });

    // KAB/KOTA → reset kecamatan, kelurahan, rw, rt
    $("#kabko").on("change", function () {
        resetSelect("#kecamatan");
        resetSelect("#kelurahan");
        resetSelect("#rw");
        resetSelect("#rt");
    });

    // KECAMATAN → reset kelurahan, rw, rt
    $("#kecamatan").on("change", function () {
        resetSelect("#kelurahan");
        resetSelect("#rw");
        resetSelect("#rt");
    });
});

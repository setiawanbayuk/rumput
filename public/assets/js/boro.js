$(document).ready(function () {
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
        minimumInputLenght: 2,
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
    $("#provinsi_boro").change(function () {
        provinsi_id = $(this).val();
        $("#kabko_boro").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                ? "100%"
                : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLenght: 2,
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
    $("#kabko_boro").change(function () {
        kabko_id = $(this).val();
        $("#kecamatan_boro").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                ? "100%"
                : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLenght: 2,
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

    $("#kecamatan_boro").change(function () {
        kecamatan_id = $(this).val();
        $("#kelurahan_boro").select2({
            theme: "bootstrap-5",
            width: $(this).data("width")
                ? $(this).data("width")
                : $(this).hasClass("w-100")
                ? "100%"
                : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLenght: 2,
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
});

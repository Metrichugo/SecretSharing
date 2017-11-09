$(document).ready(function () {
    $(document).on("click", "#modificarCuenta", function () {
        var disp = $(this).attr("data-disp");
        if (disp === "true") {
            $("#modalModificarCuenta").modal("show");
        }
    });

    //validacion de contraseña
    $(document).on("click", "#modalAceptar", function () {
        var password = $("#contraseniaModal").val();

        $.ajax({
            type: "POST",
            url: "../php/manejadorUsuario.php",
            data: {
                Operation: "validarPassword",
                password: password
            },
            success: function (response) {
                console.log(response);
                if (response === "correct") {
                    window.open("./ModificarCuenta.php", "_self");
                } else {
                    console.log(response);
                    $("#ErrorContrasenia").html('<div class="alert alert-danger"><button type="button" class="close">×</button>La contraseña no es valida </div>');
                    window.setTimeout(function () {
                        $(".alert").fadeTo(100, 0).slideUp(100, function () {
                            $(this).remove();
                        });
                    }, 5000);
                    $('.alert .close').on("click", function (e) {
                        $(this).parent().fadeTo(500, 0).slideUp(500);
                    });
                }
            }
        });
    });

});
$(document).ready(function () {
    $("#contenedorModificarCuenta").hide();

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

                    $("#modalModificarCuenta").modal("hide");
                    $("#contenedorGestion").hide();
                    $("#visualizarDetalles").attr('class', 'nav-link');
                    $("#modificarCuenta").attr('class', 'nav-link  active');
                    $("#contenedorModificarCuenta").show();
                    $("#modificarCuenta").attr('data-disp', 'false');

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



    //cambio de nombre de usuario

    /* 0 se logro cambiar
     * 1 no por ser igual a contraseñ2a
     * 2 no por duplicida4d
     * 3 cualquier otro error
     */

    $("#formNombreUsuario").submit(function () {
        var email = $("#Email").val();
        var confirmaEmail = $("#confirmaEmail").val();

        var validarReglasEmail = validarNombreUsuario(email, confirmaEmail);
        if (!validarReglasEmail)
            return false;


        var nombreLocal = getNombreLocal(email);

        $.ajax({
            type: "POST",
            url: "../php/manejadorUsuario.php",
            data: {
                Operation: "cambiarNombreUsuario",
                newNombreUsuario: email,
                newNombreLocal: nombreLocal
            },
            success: function (response) {


                switch (response) {
                    case "0":
                        mostrarRes("#ResCambiarEmail", "Se ha cambiado el nombre de usuario de forma satisfactoria");
                        break;
                    case "1":
                        mostrarError("#ErrorCambiarEmail", "Lo sentimos. El usuario y la contraseña no pueden ser iguales");
                        break;
                    case "2":
                        mostrarError("#ErrorCambiarEmail", "Lo sentimos. Ya existe un usuario usando " + email);
                        break;
                    case "3":
                        mostrarError("#ErrorCambiarEmail", "Lo sentimos. No se pudo cambiar el nombre de usauario debido a un error interno");
                        break;
                }

            }
        });

        return false;
    });

    //cambio de contraseña
    $(document).on("click", "#cambiarPassword", function () {
        var email = $("#nombreUsuario").attr("data-nombreUsuario");
        var password = $("#password").val();
        var confirmacion = $("#confirmaPassword").val();
        var validarReglasPass = validarPassword(email, password, confirmacion);
        if (!validarReglasPass)
            return false;

        $.ajax({
            type: "POST",
            url: "../php/manejadorUsuario.php",
            data: {
                Operation: "cambiarPassword",
                newPassword: password
            },
            success: function (response) {
                if (response === "correct") {
                    mostrarRes("#ResPassword", "Se ha cambiado cambiado la contraseña");

                } else {
                    mostrarError("#ErrorPassword", "Lo sentimos. No se pudo cambiar la contraseña debido a un error interno");
                }
            }
        });
    });

});
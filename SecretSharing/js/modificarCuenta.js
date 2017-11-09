$(document).ready(function () {
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
        $.ajax({
            type: "POST",
            url: "../php/manejadorUsuario.php",
            data: {
                Operation: "cambiarNombreUsuario",
                newNombreUsuario: email
            },
            success: function (response) {
                switch (response) {
                    case "0":
                        muestaMensajeOk("Se ha cambiado el nombre de usuario correctamente", "ResCambiarEmail");
                        $("#staticEmail").val(email);
                        break;
                    case "1":
                        muestraMensajeError("El usuario y la contraseña no pueden ser iguales", "ErrorCambiarEmail");
                        break;
                    case "2":
                        muestraMensajeError("Ya existe un usuario usando " + email, "ErrorCambiarEmail");
                        break;
                    case "3":
                        muestraMensajeError("No se pudo cambiar el nombre de usuario debido a un error interno", "ErrorCambiarEmail");
                        break;
                    case "4":
                        muestraMensajeError("La longitud del correo tiene que ser entre 8 y 255 caracteres", "ErrorCambiarEmail");
                        break;
                }
            }
        });

        return false;
    });

    //cambio de contraseña
    $(document).on("click", "#cambiarPassword", function () {
        var email = $("#staticEmail").val();
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
                    muestaMensajeOk("Se ha cambiado cambiado la contraseña correctamente", "ResPassword");

                } else {
                    muestraMensajeError("Lo sentimos. No se pudo cambiar la contraseña debido a un error interno", "ErrorPassword");
                }
            }
        });
    });



    //Eliminación de cuenta 
    $(document).on("click", "#modalEliminarAceptar", function () {

        $.ajax({
            type: "POST",
            url: "../php/manejadorUsuario.php",
            data: {
                Operation: "EliminarUsuario"
            },
            success: function (response) {
                console.log(response);

                if (response === "correct") {
                    muestaMensajeOk("Se ha eliminado su cuenta con correctamente", "OkEliminarCuenta");
                    setTimeout(function () {
                        window.open("../index.html", "_self");
                    }, 2000);

                } else {
                    muestraMensajeError("Lo sentimos. No se pudo eliminar su cuenta debido a un error interno", "ErrorEliminarCuenta");
                }
            }
        });
    });

});
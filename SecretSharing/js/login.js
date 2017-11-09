$.getScript("./js/funcionesComunes.js", function () {
    console.log("Funciones comunes cargadas");
});

$(document).ready(function () {
    //Recuperación de cuenta
    $("#formRecuperarCuenta").submit(function () {
        console.log("Recuperación de cuenta");
        var email = $("#correo").val();
        console.log(email);
        $.ajax({
            type: "POST",
            url: "./php/manejadorUsuario.php",
            data: {
                Operation: "recuperarCuenta",
                Email: email
            },
            success: function (response) {
                console.log(response);

                if (response === "correct") {
                    muestaMensajeOk("Se ha enviado un enlace al correo indicado", "OkRecuperarCuenta");
                    setTimeout(function () {
                        window.open("./login.html", "_self");
                    }, 2000);

                } else {
                    muestraMensajeError("No se pudo enviar el correo a la cuenta especificada", "ErrorRecuperarCuenta", );
                }
            }
        });
        return false;
    });
});

//Accion login
function submitdata() {
    var email = $('#Email').val();
    var password = $('#Password').val();
    /*If userinput password & email are correct*/

    $.ajax({
        type: "POST",
        url: "./php/manejadorUsuario.php",
        data: {
            Operation: "iniciarSesion",
            Email: email,
            Password: password
        },
        success: function (response) {
            if (response === "correct") {
                window.open("./php/Principal.php", "_self");
            } else {
                console.log(response);
                muestraMensajeError("El correo electrónico y/o contraseña no son válidos", 'Error');
            }
        }
    });
    return false;
}

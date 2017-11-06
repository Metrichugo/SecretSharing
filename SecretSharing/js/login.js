$.getScript("./js/funcionesComunes.js", function () {
    console.log("Funciones comunes cargadas");
});

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

$.getScript("./js/funcionesComunes.js", function () {
    console.log("Funciones comunes cargadas");
});

function validarContraseña(email, password, conf) {
    var res = false, res2 = false, res3 = false;
    var exp = /^(?=.*\d)(?=.*[\u0021-\u002f\u003A-\u0040\u005B-\u005F])(?=.*[A-Z])(?=.*[a-z])\S{8,64}$/;
    /* Validate if password is not equal to email */
    if (!(password.search(email.split("@")[0]) >= 0)) {
        res = true;
    } else {
        muestraMensajeError("Contraseña no válida: La contraseña no puede ser la misma que nombre de usuario (email)", 'ErrorPassword');
    }
    /*Confirmation is not equal to the password */
    if (password === conf) {
        res3 = true;
    } else {
        muestraMensajeError("Contraseña no válida: Las contraseñas no coinciden", 'ErrorPassword');
    }

    res2 = exp.test(password);
    if (!res2) {
        muestraMensajeError("Contraseña no válida: La contraseña debe de tener al menos 1 mayúscula, 1 minúscula, 1 caracter especial y 1 número; además de una longitud entre 8 y 64 caracteres", 'ErrorPassword');
    }
    return res && res2 && res3;
}

function validarAlias(alias) {
    if (alias.length > 255) {
        muestraMensajeError("Alias no válido: El alias no puede ser mayor a 255 caracteres", 'ErrorAlias');
        return false;
    }
    return true;
}


function submitdata() {
    var email = $('#Email').val();
    var password = $('#Password').val();
    var alias = $('#Alias').val();
    var conf = $('#Confimacion').val();
    if (!validarAlias(alias) || !validarContraseña(email, password, conf)) {
        return false;
    }
    console.log(email + " " + password + " " + alias + " " + conf);
    $.ajax({
        type: "POST",
        url: "php/manejadorUsuario.php",
        data: {
            Operation: "registrarUsuario",
            Email: email,
            Password: password,
            Alias: alias
        },
        success: function (response) {
            if (response === "correct") {
                //Registro correcto
                muestaMensajeOk("Usuario registrado <strong>correctamente</strong>", "RegistroOK");
            } else if (response === "duplicated") {
                //Error durante el registro
                muestraMensajeError("El correo electrónico ya existe, intente con otro o inicie sesión", "ErrorEmail");
            } else if (response === "invalidrequest") {
                muestraMensajeError("El correo electrónico o la contraseña son inválidos", "ErrorEmail");
            } else {
                muestraMensajeError("Ocurrió un error interno durante el registro", "ErrorEmail");
            }
        }

    });
    return false;
}



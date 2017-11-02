function cerrarSesion() {
    $.ajax({
        type: "POST",
        url: "manejadorUsuario.php",
        data: {
            Operation: "cerrarSesion",
        },
        success: function (response) {
            console.log("Cierre de sesion: " + response);
            if (response === "correct") {
                window.open("../login.html", "_self");
            } else {
                console.log("No se pudo cerrar sesión");
            }
        }
    });
    return true;

}


function mostrarError(elemento, mensaje) {
    $(elemento).html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">' + mensaje + '</p></div>');
    window.setTimeout(function () {
        $(".alert").fadeTo(100, 0).slideUp(100, function () {
            $(this).remove();
        });
    }, 5000);
    $('.alert .close').on("click", function (e) {
        $(this).parent().fadeTo(500, 0).slideUp(500);
    });
}
function mostrarRes(elemento, mensaje) {
    $(elemento).html('<div class="alert alert-success"><button type="button" class="close">×</button><p class="text-justify">' + mensaje + '</p></div>');
    window.setTimeout(function () {
        $(".alert").fadeTo(100, 0).slideUp(100, function () {
            $(this).remove();
        });
    }, 5000);
    $('.alert .close').on("click", function (e) {
        $(this).parent().fadeTo(500, 0).slideUp(500);
    });
}


//CONTRASEÑA

////rn-c1 caracteres validos 
//rn-c2 longitud de la contraseña
//rn-c3 robustez


function robustezPassword(password) {
    var exp = /^(?=.*\d)(?=.*[\u0021-\u002f\u003A-\u0040\u005B-\u005F])(?=.*[A-Z])(?=.*[a-z])\S{8,64}$/;
    var res = exp.test(password);
    if (!res) {
        var mensaje = "Contraseña no válida: La contraseña debe de tener al menos 1 mayúscula, 1 minúscula, 1 caracter especial y 1 número; además de una longitud entre 8 y 64 caracteres";
        mostrarError('#ErrorPassword', mensaje);
    }
    return res;
}

//rn-c4 contraseña diferente de nombre usuario
function passwordNotEmail(password, email) {
    email = email.split("@")[0];
    console.log(email);
    if ((password.search(email) >= 0)) {
        var mensaje = "Contraseña no válida: La contraseña no puede ser la misma que nombre de usuario (email)";
        mostrarError('#ErrorPassword', mensaje);
        return false;
    }
    return true;
}
//rn-c? contraseña igual a confirmacion 
function passwordEqualConf(password, conf) {
    if (password === conf) {
        return true;
    } else {
        var mensaje = "Contraseña no válida: Las contraseñas no coinciden";
        mostrarError('#ErrorPassword', mensaje);
    }
    return false;
}

function validarPassword(email, password, conf) {
    var r1 = false, r2 = false, r3 = false;
    r3 = passwordEqualConf(password, conf);
    if(!r3) return r3;
    r1 = robustezPassword(password);
    if(!r1) return r1;
    r2 = passwordNotEmail(password, email);
    if(!r2) return r2;
    return true;
}



//NOMBRE DE USUARIO

function getNombreLocal(email) {
    return email.split("@")[0];
}

function usuarioEqualConf(email, confirmaEmail) {
    if (email === confirmaEmail)
        return true;
    else {
        var mensaje = "Error. El nombre de usuario y la confirmación no coinciden";
        mostrarError('#ErrorCambiarEmail', mensaje);
    }
    return false;
}

function validarNombreUsuario(email, confirmaEmail) {
    var r1 = usuarioEqualConf(email, confirmaEmail);
    return r1;
}




function validatePassword(email, password, conf) {
    var res = false, res2 = false, res3 = false;
    var exp = /^(?=.*\d)(?=.*[\u0021-\u002f\u003A-\u0040\u005B-\u005F])(?=.*[A-Z])(?=.*[a-z])\S{8,64}$/;
    /* Validate if password is not equal to email */
    if (!(password.search(email.split("@")[0]) >= 0)) {
        res = true;
    } else {
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Contraseña no válida: La contraseña no puede ser la misma que nombre de usuario (email)</p></div>');
        window.setTimeout(function () {
            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                $(this).remove();
            });
        }, 5000);
        $('.alert .close').on("click", function (e) {
            $(this).parent().fadeTo(500, 0).slideUp(500);
        });
    }
    /*Confirmation is not equal to the password */
    if (password === conf) {
        res3 = true;
    } else {
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Contraseña no válida: Las contraseñas no coinciden</p></div>');
        window.setTimeout(function () {
            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                $(this).remove();
            });
        }, 5000);
        $('.alert .close').on("click", function (e) {
            $(this).parent().fadeTo(500, 0).slideUp(500);
        });
    }

    res2 = exp.test(password);
    if (!res2) {
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Contraseña no válida: La contraseña debe de tener al menos 1 mayúscula, 1 minúscula, 1 caracter especial y 1 número; además de una longitud entre 8 y 64 caracteres</p></div>');
        window.setTimeout(function () {
            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                $(this).remove();
            });
        }, 5000);
        $('.alert .close').on("click", function (e) {
            $(this).parent().fadeTo(500, 0).slideUp(500);
        });
    }
    return res && res2 && res3;
}

function validateAlias(alias) {
    //document.write(alias);
    var res = false;
    if (alias.length > 255) {
        $('#ErrorAlias').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Alias no válido: El alias no puede ser mayor a 255 caracteres</p></div>');
        window.setTimeout(function () {
            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                $(this).remove();
            });
        }, 5000);
        $('.alert .close').on("click", function (e) {
            $(this).parent().fadeTo(500, 0).slideUp(500);
        });
        return false;
    }
    return true;
}


function submitdata() {
    var email = $('#Email').val();
    var password = $('#Password').val();
    var alias = $('#Alias').val();
    var conf = $('#Confimacion').val();
    if (!validateAlias(alias) || !validatePassword(email, password, conf)) {
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
                $('#RegistroOK').html('<div class="alert alert-success"><button type="button" class="close">×</button>Usuario registrado <strong>correctamente</strong></div>');
                window.setTimeout(function () {
                    $(".alert").fadeTo(100, 0).slideUp(100, function () {
                        $(this).remove();
                        window.open("login.html", "_self");
                    });
                }, 3000);

                $('.alert .close').on("click", function (e) {
                    $(this).parent().fadeTo(500, 0).slideUp(500);
                });


            } else {
                //Error durante el registro
                $('#ErrorEmail').html('<div class="alert alert-danger"><button type="button" class="close">×</button>El correo electrónico ya existe, intente con otro o inicie sesión</div>');
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
    return false;


}


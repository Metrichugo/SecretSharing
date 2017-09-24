function validatePassword(email, password,conf) {
    var res = false, res2 = false,res3 = false;
    var exp = /^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,64}$/;
    /* Validate if password is not equal to email */
    if (!(password.search(email.split("@")[0]) >= 0)) {
        res = true;
    }else{
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">La contraseña no puede ser la misma que nombre de usuario(email)</p></div>');
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
    if(password==conf){
        res3 = true;
    }else{
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Las contraseñas no coinciden</p></div>');
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
    if(!res2){
        $('#ErrorPassword').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">Las contraseñas debe de tener al menos 1 mayúscula, 1 minúscula, 1 caracter especial, 1 número y de longitud entre 8 y 64 caracteres</p></div>');
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

function validateAlias(alias){
    //document.write(alias);
    var res=false;
    if(alias.length>255){
        $('#ErrorAlias').html('<div class="alert alert-danger"><button type="button" class="close">×</button><p class="text-justify">El alias no puede ser mayor a 255 caracteres</p></div>');
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


function submitdata(){
    var email = $('#Email').val();
    var password = $('#Password').val();
    var alias = $('#Alias').val();
    var conf = $('#Confimacion').val(); 
    /* Aa@1qwerty*/
    if( !validateAlias(alias) || !validatePassword(email, password,conf)){
        return false;
    }
    $.ajax({
        type: "POST",
        url: "php/SignUp.php",
        data: {
            Email: email,
            Password: password,
            Alias : alias
        },
        success: function (response) {
            if (response == "correct") {
                window.open("index.html", "_self");
            } else {
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



/*$('#Error').html('<div class="alert alert-danger"><button type="button" class="close">×</button>El correo electrónico y/o contraseña no son válidos</div>');
window.setTimeout(function () {
    $(".alert").fadeTo(100, 0).slideUp(100, function () {
        $(this).remove();
    });
}, 5000);
/* Button for close alert */
/*$('.alert .close').on("click", function (e) {
    $(this).parent().fadeTo(500, 0).slideUp(500);
});*/

/*
    if (validatePassword(email, password)) {


    }else {
    $('#Error').html('<div class="alert alert-danger"><button type="button" class="close">×</button>Usuario y/o contaseña no validos </div>');
    window.setTimeout(function () {
        $(".alert").fadeTo(100, 0).slideUp(100, function () {
            $(this).remove();
        });
    }, 5000);

    $('.alert .close').on("click", function (e) {
        $(this).parent().fadeTo(500, 0).slideUp(500);
    });
}*/
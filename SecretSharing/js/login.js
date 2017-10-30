function submitdata() {
    var email = $('#Email').val();
    var password = $('#Password').val();
    /*If userinput password & email are correct*/

    $.ajax({
        type: "POST",
        url: "php/Login.php",
        data: {
            Operation: "iniciarSesion",
            Email: email,
            Password: password
        },
        success: function (response) {
            if (response === "correct") {
                window.open("./php/Principal.php","_self");
            } else {
                console.log(response);
                $('#Error').html('<div class="alert alert-danger"><button type="button" class="close">×</button>El correo electrónico y/o contraseña no son válidos</div>');
                window.setTimeout(function () {
                    $(".alert").fadeTo(100, 0).slideUp(100, function () {
                        $(this).remove();
                    });
                }, 5000);
                /* Button for close alert */
                $('.alert .close').on("click", function (e) {
                    $(this).parent().fadeTo(500, 0).slideUp(500);
                });
            }
        }
    });
    return false;
}

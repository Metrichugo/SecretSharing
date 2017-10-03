/****** Funciones para actualizar contenido en pantalla principal *****************/
function actualizarCarpetaActual(idCarpetaMoverse){
   // ajax para actualizar la variable de sesion de la carpeta actual
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarCarpetaActual",
            idCarpetaMoverse: idCarpetaMoverse
        },
        success: function (response) {
          //  document.write(response);
        }
    }); 
}

function actualizarCarpetasEnPantalla(){
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarCarpetasEnPantalla"
        },
        success: function (response) {

            //document.write("hola");
            $('#contenedorCarpetas').empty();
            $('#contenedorCarpetas').html(response);
        }
    });
    return true;
}

function actualizarArchivosEnPantalla(){
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarArchivosEnPantalla"
        },
        success: function (response) {

            //document.write("hola");
            $('#contenedorArchivos').empty();
            $('#contenedorArchivos').html(response);
        }
    });
    return true;
}

function actualizarContenidoEnPantalla(idCarpetaMoverse) {
    
    actualizarCarpetaActual(idCarpetaMoverse);
    actualizarCarpetasEnPantalla();
    actualizarArchivosEnPantalla();
}
function irCarpetaAtras() {
    // ajax para actualizar volver a la carpeta superior
    var flag = true;
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "irCarpetaAtras"
        },
        success: function (response) {
            if(response.localeCompare("incorrect") != 0 ){
                actualizarCarpetasEnPantalla();
                actualizarArchivosEnPantalla();
            }  
        }
    });
}



function validarNombreCarpeta(nomCarpeta){

    var r1 = true, r2 = true , r3 = true;
    if(nomCarpeta.length >= 255 ){
        r1 = false;
        $('#ErrorNombreCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>El nombre de la carpeta no puede exceder los 255 caracteres</div>');
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
    if(nomCarpeta.indexOf("/") != -1 ){
        r2 = false;
        $('#ErrorNombreCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>El carácter / no es valido</div>');
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
    if(nomCarpeta.localeCompare(".") == 0  || nomCarpeta.localeCompare("..") == 0 ){
        r3 = false;
        $('#ErrorNombreCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>La carpeta no puede llamarse . o ..</div>');
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

    return r1 && r2 && r3;
}

function crearNuevaCar(){
    var nombreCarpeta = $('#nombreCarpeta').val();
    var flag = validarNombreCarpeta(nombreCarpeta);
    
    if(!flag) return false;
    //Nombre de carpeta, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
           Operation : "crearNuevaCar",
           nombreCarpeta: nombreCarpeta
        },
        success: function (response) {
            if (response == "correct"){

                $('#resultadoCrearCarpeta').html('<div class="alert alert-success"><button type="button" class="close">×</button>Nueva carpeta creada</div>');
                        window.setTimeout(function () {
                            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                                $(this).remove();
                            });
                        }, 5000);
                        /* Button for close alert */
                        $('.alert .close').on("click", function (e) {
                            $(this).parent().fadeTo(500, 0).slideUp(500);
                        });

                actualizarCarpetasEnPantalla();
            }else{
                $('#resultadoCrearCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>La carpeta ya existe.!</div>');
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

function eliminarCarpeta( idCarpeta ){
    console.log(idCarpeta);
    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
           Operation : "eliminarCarpeta",
           idCarpeta: idCarpeta
        },
        success: function (response) {
            console.log(response);
            if (response == "correct"){
                actualizarCarpetasEnPantalla();
            }else{
                console.log("no pude");
            }
        }
    });

}


//script para cargar los datos de la carpeta raiz
function cargarCarpetaRaiz(  ){

    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
           Operation : "cargarCarpetaRaiz"
        },
        success: function (response) {
            console.log(response);
            actualizarContenidoEnPantalla(response);

            return response;
        }
    });
}

cargarCarpetaRaiz();
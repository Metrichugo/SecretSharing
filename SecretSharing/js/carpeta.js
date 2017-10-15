/****** Funciones para actualizar contenido en pantalla principal *****************/
var idCarpetaGlobal = 0;

$(document).ready(function () {
    $(document).on("click", ".btn-sel-carp", function () {
        idCarpetaGlobal = $(this).attr("data-idCarpeta");
        console.log("desde  btn-sel-carp" + idCarpetaGlobal);

    });
});

function editarNombreCarpeta() {

    idCarpetaEditar = idCarpetaGlobal;
    var nombreCarpeta = $('#nombreEditarCarpeta').val();
    var flag = validarNombreCarpeta(nombreCarpeta, "ErrorEditarCarpeta");
    if (!flag)
        return false;

    //Nombre de carpeta, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
            Operation: "EditarCar",
            nombreCarpeta: nombreCarpeta,
            idCarpetaEditar: idCarpetaEditar
        },
        success: function (response) {
            if (response === "correct") {

                $('#resultadoEditarCarpeta').html('<div class="alert alert-success"><button type="button" class="close">×</button>Se ha actualizado del nombre de la carpeta</div>');
                window.setTimeout(function () {
                    $(".alert").fadeTo(100, 0).slideUp(100, function () {
                        $(this).remove();
                    });
                }, 5000);
                /* Button for close alert */
                $('.alert .close').on("click", function (e) {
                    $(this).parent().fadeTo(500, 0).slideUp(500);
                });

                //Timeout cerrar modal
                setTimeout(function () {
                    $('#modalEditarCarpeta').modal('hide');
                }, 1000);

                actualizarCarpetasEnPantalla();
            } else {
                $('#resultadoEditarCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>No se puede renombrar la carpeta: Existe una carpeta con el mismo nombre</div>');
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


function actualizarCarpetaActual(idCarpetaMoverse) {
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

function actualizarCarpetasEnPantalla() {
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

function actualizarArchivosEnPantalla() {
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
            if (response.localeCompare("incorrect") !== 0) {
                actualizarCarpetasEnPantalla();
                actualizarArchivosEnPantalla();
            }
        }
    });
}

function validarNombreCarpeta(nomCarpeta, classError) {
    var r1 = true, r2 = true, r3 = true, r4 = true;
    if (nomCarpeta.length >= 255) {
        r1 = false;
        muestraMensajeError("El nombre de la carpeta no puede exceder los 255 caracteres");
    }
    if (nomCarpeta.indexOf("/") !== -1) {
        r2 = false;
        muestraMensajeError("La carpeta no puede contener el caracter /");
    }

    if (nomCarpeta.localeCompare(".") === 0 || nomCarpeta.localeCompare("..") === 0) {
        r3 = false;
        muestraMensajeError("La carpeta no puede llamarse . o ..", classError);
    }

    if (nomCarpeta.length === 0) {
        r4 = false;
        muestraMensajeError("El nombre de la carpeta debe contener al menos un caracter", classError);
    }
    return r1 && r2 && r3 && r4;
}

function muestraMensajeError(mensaje, classError) {
    $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>' + mensaje + '</div>');
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

function crearNuevaCarpeta() {
    var nombreCarpeta = $('#nombreCarpeta').val();
    var flag = validarNombreCarpeta(nombreCarpeta, "ErrorNombreCarpeta");

    if (!flag)
        return false;
    //Nombre de carpeta, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
            Operation: "crearNuevaCar",
            nombreCarpeta: nombreCarpeta
        },
        success: function (response) {
            if (response === "correct") {

                $('#resultadoCrearCarpeta').html('<div class="alert alert-success"><button type="button" class="close">×</button>Carpeta creada correctamente</div>');
                window.setTimeout(function () {
                    $(".alert").fadeTo(100, 0).slideUp(100, function () {
                        $(this).remove();
                    });
                }, 5000);
                /* Button for close alert */
                $('.alert .close').on("click", function (e) {
                    $(this).parent().fadeTo(500, 0).slideUp(500);
                });

                //Timeout cerrar modal
                setTimeout(function () {
                    $('#modalCrearCarpeta').modal('hide');
                }, 1000);

                actualizarCarpetasEnPantalla();
            } else {
                $('#resultadoCrearCarpeta').html('<div class="alert alert-danger"><button type="button" class="close">×</button>Ya existe una carpeta con el mismo nombre</div>');
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

    //Resetar nombre de la carpeta del modal

    return false;
}

function eliminarCarpeta( ) {

    idCarpeta = idCarpetaGlobal;

    console.log("Desde eliminar carpeta () " + idCarpeta);

    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
            Operation: "eliminarCarpeta",
            idCarpeta: idCarpeta
        },
        success: function (response) {
            console.log(response);
            if (response === "correct") {
                actualizarCarpetasEnPantalla();
                $('#modalEliminarCarpeta').modal('hide');
            } else {
                console.log("no pude");
            }
        }
    });

    return true;
}
function cargarCarpetaRaiz(  ) {

    $.ajax({
        type: "POST",
        url: "manejoCarpeta.php",
        data: {
            Operation: "cargarCarpetaRaiz"
        },
        success: function (response) {
            console.log(response);
            actualizarContenidoEnPantalla(response);

            return response;
        }
    });
}


cargarCarpetaRaiz();

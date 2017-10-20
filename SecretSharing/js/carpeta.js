/****** Funciones para actualizar contenido en pantalla principal *****************/
var idCarpetaGlobal = 0;

$(document).ready(function () {
    $(document).on("click", ".btn-sel-carp", function () {
        idCarpetaGlobal = $(this).attr("data-idCarpeta");
        console.log("desde  btn-sel-carp: " + idCarpetaGlobal);

    });

    //Hace que los modales se reinicien cada que se ocultan/cierran
    $('.modal').on('hidden.bs.modal', function () {
        $('.modal-body').find('input,text').val('');
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

                $('#' + idCarpetaEditar).text(nombreCarpeta);

                //Timeout cerrar modal
                setTimeout(function () {
                    $('#modalEditarCarpeta').modal('hide');
                }, 1000);

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

function actualizarCarpetas() {
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarCarpetas"
        },
        success: function (response) {
            $('#tablaCarpetas').append(response);
        }
    });
    return true;
}

// Se agrego este metodo
function listarCarpetas() {
    $('#tablaCarpetas').empty();
    actualizarCarpetas();
}

//Se agregó este metodo(Cambiar nombre o inclusive quitarlo y poner unicamente empty donde lo requiera al igual que para listar carpetas)
function listarArchivos() {
    $('#tablaArchivos').empty();
    actualizarArchivos();
}


function actualizarArchivos(){
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarArchivos"
        },
        success: function (response) {
            $('#tablaArchivos').append(response);
        }
    });
    return true;
}

function actualizarContenidoEnPantalla(idCarpetaMoverse) {
    actualizarCarpetaActual(idCarpetaMoverse);
    listarCarpetas();
    listarArchivos();
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
            if (response.localeCompare("incorrect") != 0) {
                listarCarpetas();
                listarArchivos();
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
        muestraMensajeError("La carpeta no puede contener el caracter /", classError);
    }

    if (nomCarpeta.localeCompare(".") === 0 || nomCarpeta.localeCompare("..") === 0) {
        r3 = false;
        muestraMensajeError("La carpeta no puede llamarse . o ..", classError);
    }

    if (nomCarpeta.trim().length === 0) {
        r4 = false;
        muestraMensajeError("El nombre de la carpeta debe contener al menos un caracter", classError);
    }
    return r1 && r2 && r3 && r4;
}

//Se Modifico este método
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
            var jsonResponse = $.parseJSON(response);
            if (jsonResponse.Status === "correct") {
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
                $('#tablaCarpetas').append(jsonResponse.Html);
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
                $('#modalEliminarCarpeta').modal('hide');
                $('#row' + idCarpeta).remove();
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


cargarCarpetaRaiz();

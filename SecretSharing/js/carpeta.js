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
        url: "manejadorCarpeta.php",
        data: {
            Operation: "EditarCar",
            nombreCarpeta: nombreCarpeta,
            idCarpetaEditar: idCarpetaEditar
        },
        success: function (response) {
            if (response === "correct") {
                muestaMensajeOk("Se ha actualizado del nombre de la carpeta", "resultadoEditarCarpeta");
                //Actualiza el nombre en pantalla
                $('#' + idCarpetaEditar).text(nombreCarpeta);
                //Timeout cerrar modal
                setTimeout(function () {
                    $('#modalEditarCarpeta').modal('hide');
                }, 1000);

            } else {
                muestraMensajeError("No se puede renombrar la carpeta: Existe una carpeta con el mismo nombre", "resultadoEditarCarpeta");
            }
        }
    });
    return false;
}


function actualizarCarpetaActual(idCarpetaMoverse) {
    // ajax para actualizar la variable de sesion de PHP de la carpeta actual
    $.ajax({
        type: "POST",
        url: "../php/manejadorCarpeta.php",
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
        url: "../php/manejadorCarpeta.php",
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


function actualizarArchivos() {
    // AJAX para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejadorCarpeta.php",
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
    // AJAX para actualizar volver a la carpeta superior
    $.ajax({
        type: "POST",
        url: "../php/manejadorCarpeta.php",
        data: {
            Operation: "irCarpetaAtras"
        },
        success: function (response) {
            if (response.localeCompare("incorrect") !== 0) {
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

//Se modifico este método
function crearNuevaCarpeta() {
    var nombreCarpeta = $('#nombreCarpeta').val();
    var flag = validarNombreCarpeta(nombreCarpeta, "ErrorNombreCarpeta");

    if (!flag)
        return false;
    //Nombre de carpeta, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejadorCarpeta.php",
        data: {
            Operation: "crearNuevaCar",
            nombreCarpeta: nombreCarpeta
        },
        success: function (response) {
            var jsonResponse = $.parseJSON(response);
            if (jsonResponse.Status === "correct") {
                muestaMensajeOk("Nueva carpeta creada", "resultadoCrearCarpeta");
                //Actualiza contenido en pantalla
                $('#tablaCarpetas').append(jsonResponse.Html);
                //Timeout cerrar modal
                setTimeout(function () {
                    $('#modalCrearCarpeta').modal('hide');
                }, 1000);
            } else {
                muestraMensajeError("Ya existe una carpeta con el mismo nombre", "resultadoCrearCarpeta");
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
        url: "manejadorCarpeta.php",
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
                console.log("Error al eliminar la carpeta");
            }
        }
    });

    return true;
}


function cargarCarpetaRaiz() {
    $.ajax({
        type: "POST",
        url: "manejadorCarpeta.php",
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

function muestaMensajeOk(mensaje, classOK) {
    $('#' + classOK).html('<div class="alert alert-success"><button type="button" class="close">×</button>' + mensaje + '</div>');
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

$(document).ready(function () {
    $('#modalMoverCarpeta').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var idCarpeta = $(opener).attr('data-idCarpeta');
        console.log("Desde la carpeta:" + idCarpeta);
        $.ajax({
            type: "POST",
            url: "manejadorCarpeta.php",
            data: {
                Operation: "obtenerSubCarpetas",
                idCarpeta: idCarpeta
            },
            success: function (response) {
                console.log(response);
                $('#selectCarpetas').empty();
                $('#selectCarpetas').append(response);
                $('#moveCarpeta').attr("data-idCarpeta", idCarpeta);
                var numSubCarpetas = $('#selectCarpetas > option').length;
                if (numSubCarpetas === 0) {
                    $('#moveCarpeta').attr("disabled", "disabled");
                    console.log("Subcarpetas existentes: " + numSubCarpetas);
                } else {
                    $('#moveCarpeta').removeAttr("disabled");
                    console.log("Hay carpetas");
                }
            }
        });
    });
});

function moverCarpeta() {
    var idCarpetaDest = $('#selectCarpetas').val();
    var idCarpeta = $('#moveCarpeta').attr("data-idCarpeta");
    $.ajax({
        type: "POST",
        url: "manejadorCarpeta.php",
        data: {
            Operation: "moverCarpeta",
            idCarpetaDest: idCarpetaDest,
            idCarpeta: idCarpeta
        },
        success: function (response) {
            if (response === "Se movio la carpeta") {
                muestaMensajeOk("La carpeta se movio correctamente", "resultadoMoverCarpeta");
                $('#row' + idCarpeta).remove();
                setTimeout(function () {
                    $('#modalMoverCarpeta').modal('hide');
                }, 1000);
            } else {
                muestraMensajeError("Ya existe una carpeta con el mismo nombre", "resultadoMoverCarpeta");
            }
        }
    });
}



cargarCarpetaRaiz();

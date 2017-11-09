$.getScript("../js/funcionesComunes.js", function () {
    console.log("Funciones comunes cargadas");
});


/**
 Variables globales
 cuando el usuario quiera hacer alguna operacion sobre un archivo 
 se invoca un metodo que actualiza el valor de estas variables luego se hacen
 uso de estas variables en cualquier metodo 
 **/

var idCarpetaG = 0; // la carpeta a la que pertenece el archivo
var nomArchivoG = 0; // El archivo sobre el cual se le hara  la operacion

$(document).ready(function () {
    $(document).on("click", ".btn-sel-arch", function () {
        idCarpetaG = $(this).attr("data-idCarpeta");
        nomArchivoG = $(this).attr("data-nomArchivo");
        console.log("idCarpeta  del archivo seleccionado: " + idCarpetaG);
        console.log("nombre Archivo: " + nomArchivoG);
    });
});


function eliminarArchivo() {
    $("#deleteFile").prop("disabled", true);
    console.log("Ejecutando accion de elimnacio de archivo");
    idCarpeta = $('#deleteFile').attr('data-idCarpeta');
    nombreArchivo = $('#deleteFile').attr('data-oldName');
    console.log("En funcion eliminar Archivo");
    console.log("Eliminando archivo con idCarpeta: " + idCarpeta + " y nombre de archivo: " + nombreArchivo);

    //Petición AJAX
    $.ajax({
        type: "POST",
        url: "manejadorArchivo.php",
        data: {
            Operation: "EliminarArchivo",
            nombreArchivo: nombreArchivo
        },
        success: function (response) {
            console.log("Eliminacion del archivo " + response);
            if (response === "correct") {
                $(jq('row' + nombreArchivo)).remove();
                setTimeout(function () {
                    $('#modalEliminaArchivo').modal('hide');
                }, 500);
                $("#deleteFile").prop("disabled", false);
            } else {
                console.log("No se pudo eliminar el archivo");
                $("#deleteFile").prop("disabled", false);
            }
        }
    });

    return false;

}


function validarNombreArchivo(nuevoNomArch, classError) {
    var r1 = true, r2 = true, r3 = true, r4 = true;
    //Valida longitud
    if (nuevoNomArch.length >= 255) {
        r1 = false;
        muestraMensajeError("El nombre del archivo no puede exceder los 255 caracteres", classError);
    }
    //Valida caracter '/'
    if (nuevoNomArch.indexOf("/") !== -1) {
        r2 = false;
        muestraMensajeError("El carácter / no es válido", classError);
    }
    //Valida caracteres . y ..
    if (nuevoNomArch.localeCompare(".") === 0 || nuevoNomArch.localeCompare("..") === 0) {
        r3 = false;
        muestraMensajeError("El archivo no puede llamarse . o ..", classError);
    }
    //Valida longitud diferente de 0
    if (nuevoNomArch.trim().length === 0) {
        r1 = false;
        muestraMensajeError("El nombre del archivo debe contener al menos un caracter", classError);
    }
    return r1 && r2 && r3 && r4;
}


function subirArchivo() {
    $("#botonSubirArchivo").prop("disabled", true);
    $("#botonSubirArchivo").html('Subiendo... <i class="fa fa-refresh fa-spin"></i>');

    var inputFile = document.getElementById("fileToUpload");
    var file = inputFile.files[0];
    console.log("Contenido:");
    console.log(file);

    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('Operation', 'SubirArchivo');
    //document.write(inputFile);
    $.ajax({
        type: "POST",
        url: "../php/manejadorArchivo.php",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            console.log(response);
            var jsonResponse = $.parseJSON(response);
            if (jsonResponse.Status === "UploadSuccesfull") {
                muestaMensajeOk("Archivo subido correctamente", "resultadoSubirArchivo");
                $('#tablaArchivos').append(jsonResponse.Html);
                //Timeout cerrar modal
                setTimeout(function () {
                    $('#UploadFile').modal('hide');
                }, 1000);

            } else if (jsonResponse.Status === "UploadFailed") {
                muestraMensajeError("Ocurrió un error interno en el servidor, intentelo más tarde", "errorSubirArchivo");
            } else if (jsonResponse.Status === "NoFileSelected") {
                muestraMensajeError("Seleccione un archivo", "errorSubirArchivo");
            } else if (jsonResponse.Status === "duplicated") {
                muestraMensajeError("Existe un archivo con el mismo nombre en ésta carpeta", "errorSubirArchivo");
            } else if (jsonResponse.Status === "notenoughspace") {
                muestraMensajeError("El archivo sobrepasa la capacidad del sistema: El usuario no cuenta con suficiente espacio disponible", "errorSubirArchivo");
            } else if (jsonResponse.Status === "overload") {
                muestraMensajeError("El archivo sobrepasa la capacidad del sistema: El archivo pesa mas de 1 GB", "errorSubirArchivo");
            }
            $("#botonSubirArchivo").prop("disabled", false);
            $("#botonSubirArchivo").html('Subir archivo');
        }

    });
    return false;
}

//Funciones para seleccionar correctamente el nombre del archivo cuando tiene caracteres especiales
function jq(myid) {
    return "#" + myid.replace(/(:|\.|\[|\]|,|=|@)/g, "\\$1");
}

function jq2(myid) {
    return myid.replace(/(:|\.|\[|\]|,|=|@)/g, "\\$1");
}
//

/* Se obtiene la referencia del objeto que invocó al modal, se obtienen sus
 valores y se ponen como atributos para ser utilizados posteriormente*/
//Editar
$(document).ready(function () {
    $('#modalEditarArchivo').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var oldName = $(opener).attr('data-nomArchivo');
        var idCarpeta = $(opener).attr('data-idCarpeta');
        $('#nombreEditarArchivo').attr("data-oldName", oldName);
        $('#nombreEditarArchivo').attr("data-idCarpeta", idCarpeta);
    });
});

function editarNombreArchivo() {
    var idCarpeta = $('#nombreEditarArchivo').attr('data-idCarpeta');
    var nombreArch = $('#nombreEditarArchivo').attr('data-oldName');
    var nuevoNomArch = $('#nombreEditarArchivo').val();
    var flag = validarNombreArchivo(nuevoNomArch, "ErrorEditarArchivo");
    if (!flag)
        return false;
    console.log("Nombre del Archivo " + nombreArch + " Nuevo Nombre " + nuevoNomArch);
    //Nombre de archivo, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejadorArchivo.php",
        data: {
            Operation: "EditarArchivo",
            nombreArchivo: nombreArch,
            nuevoNomArch: nuevoNomArch
        },
        success: function (response) {
            console.log(response);
            if (response === "correct") {
                $(jq('row' + nombreArch)).attr('id', jq2('row' + nuevoNomArch));
                $(jq('arch' + nombreArch)).text(nuevoNomArch);
                $(jq('arch' + nombreArch)).attr('id', 'arch' + nuevoNomArch);
                $(jq('mov' + nombreArch)).attr('data-nomArchivo', nuevoNomArch);

                $(jq('down' + nombreArch)).attr('data-nomArchivo', nuevoNomArch);
                $(jq('edit' + nombreArch)).attr('data-nomArchivo', nuevoNomArch);
                $(jq('del' + nombreArch)).attr('data-nomArchivo', nuevoNomArch);
                $(jq('down' + nombreArch)).attr('id', 'down' + nuevoNomArch);
                $(jq('edit' + nombreArch)).attr('id', 'edit' + nuevoNomArch);
                $(jq('del' + nombreArch)).attr('id', 'del' + nuevoNomArch);
                $(jq('mov' + nombreArch)).attr('id', 'mov' + nuevoNomArch);

                //Mostrar mensaje de confirmación 
                muestaMensajeOk("Se ha actualizado del nombre del archivo", "resultadoEditarArchivo");
                //Cerrar modal
                setTimeout(function () {
                    $('#modalEditarArchivo').modal('hide');
                }, 500);

            } else {
                muestraMensajeError("Ya existe un archivo con el mismo nombre", "resultadoEditarArchivo");
            }
        }
    });
    return false;
}

//Eliminar
$(document).ready(function () {
    $('#modalEliminaArchivo').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var oldName = $(opener).attr('data-nomArchivo');
        var idCarpeta = $(opener).attr('data-idCarpeta');
        $('#deleteFile').attr("data-oldName", oldName);
        $('#deleteFile').attr("data-idCarpeta", idCarpeta);
    });
});

//Descargar 
$(document).ready(function () {
    $(document).on("click", ".descargaArch", function (e) {
        //e.target.id;
        $(e.target).html('<i class="fa fa-refresh fa-spin"></i>');
        $(e.target).prop("disabled", true);

        var idCarpeta = $(this).attr("data-idCarpeta");
        var nombreArchivo = $(this).attr("data-nomArchivo");

        console.log("idCarpeta " + idCarpeta);
        console.log("nombreArchivo " + nombreArchivo);
        //Peticion de preparacion
        $.ajax({
            type: "POST",
            url: "../php/manejadorArchivo.php",
            data: {
                Operation: "prepararArchivo",
                idCarpeta: idCarpeta,
                nombreArchivo: nombreArchivo
            },
            success: function (response) {
                console.log(response);
                if (response === "correct") {
                    //Recuperacion correcta
                    console.log("Ejecutando descarga...");
                    $.AjaxDownloader({
                        url: "../php/manejadorArchivo.php",
                        data: {
                            Operation: "descargarArchivo",
                            idCarpeta: idCarpeta,
                            nombreArchivo: nombreArchivo
                        }
                    });
                    $(this).prop("disabled", false);
                    $(this).html('Descargar');
                } else {
                    muestraMensajeError("No se pudo recuperar el archivo en este momento, intente más tarde", "ErroresPrincipal");

                }
                console.log("Fin de descarga");
                $(e.target).prop("disabled", false);
                $(e.target).html(' ');
                $(e.target).html('<i class="fa fa-download" aria-hidden="true"></i>');
            }
        });

        return true;
    });
});


//Mover archivo //
$(document).ready(function () {
    $('#modalMoverArchivo').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var idCarpeta = $(opener).attr('data-idCarpeta');
        var idArchivo = $(opener).attr('data-nomArchivo');
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
                $('#selectCarpetasArch').empty();
                $('#selectCarpetasArch').append(response);
                var numSubCarpetas = $('#selectCarpetasArch > option').length;
                if (numSubCarpetas === 0) {
                    $('#moveArchivo').attr("disabled", "disabled");
                } else {
                    $('#moveArchivo').attr("data-idCarpeta", idCarpeta);
                    $('#moveArchivo').attr("data-nomArchivo", idArchivo);
                    $('#moveArchivo').removeAttr("disabled");
                    console.log("Hay carpetas");
                }
            }
        });
    });
});

function moverArchivo() {
    var idCarpetaDest = $('#selectCarpetasArch').val();
    var nomArchivo = $('#moveArchivo').attr("data-nomArchivo");
    $.ajax({
        type: "POST",
        url: "manejadorArchivo.php",
        data: {
            Operation: "moverArchivo",
            idCarpetaDest: idCarpetaDest,
            nombreArchivo: nomArchivo
        },
        success: function (response) {
            console.log(response);
            if (response === "correct") {
                muestaMensajeOk("Se movió correctamente el archivo", "resultadoMoverArchivo");
                setTimeout(function () {
                    $('#modalMoverArchivo').modal('hide');
                }, 1000);
                $(jq('row' + nomArchivo)).remove();
            } else if (response === "duplicated") {
                muestraMensajeError("Existe un archivo con el mismo nombre en la carpeta destino", "resultadoMoverArchivo");
            } else {
                muestraMensajeError("Ocurrió un error intertno al mover el archivo", "resultadoMoverArchivo");
            }
        }
    });
    return false;
}


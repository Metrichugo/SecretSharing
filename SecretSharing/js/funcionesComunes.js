/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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


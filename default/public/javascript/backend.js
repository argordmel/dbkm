/**
 * Mensajes tipo Flash
 */
function flashInfo(msg) { return flashShow(msg, 'info'); }
function flashError(msg) { return flashShow(msg, 'danger'); }
function flashValid(msg) { return flashShow(msg, 'success'); }
function flashWarning(msg) { return flashShow(msg, 'warning'); }
function flashShow(msg, type) { $(".flash-message").empty(); $('.flash-message:first').append('<div class="alert alert-block alert-'+type+'"><button class="close" data-dismiss="alert" type="button">×</button>'+msg+'</div>'); }
function flashClear() { $(".flash-message").empty(); }

/**
 * OpenPopup
 */
function popupReport(url) { var report = window.open(url , 'impresion', "width=800,height=500,left=50,top=50,scrollbars=yes,menubars=no,statusbar=NO,status=NO,resizable=YES,location=NO"); report.focus(); }

/**
 * Funciones para limpiar caracteres al igual que el trim de php
 */
function ltrim(str, opt) { if(opt) { while (str.charAt(0) == opt) str = str.substr(1, str.length - 1); } else { while (str.charAt(0) == " ") str = str.substr(1, str.length - 1); } return str; }
function rtrim(str, opt) { if(opt) { while (str.charAt(str.length - 1) == opt) str = str.substr(0, str.length - 1); } else { while (str.charAt(str.length - 1) == " ") str = str.substr(0, str.length - 1); } return str; }
function trim(str, opt) { var str = new String(str); return rtrim(ltrim(str, opt), opt); }

/** Buttons forward y back **/
$(function() { $("body").on('click', '.btn-back', function(event) { history.back();}); $("body").on('click', '.btn-forward', function(event) { history.forward();});   });

$("body").ajaxStart(function() { $("body").css("cursor", "wait");}).ajaxStop(function() { $("body").css("cursor", "default"); });

$(function() {
    $('body').on('click', 'button.disabled', function(e) {
        e.preventDefault();
        return;
    });      
});

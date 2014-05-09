/**
 * Mensajes tipo Flash
 */
function flashInfo(msg, delay) { return flashShow(msg, 'info', delay); }
function flashError(msg, delay) { return flashShow(msg, 'danger', delay); }
function flashValid(msg, delay) { return flashShow(msg, 'success', delay); }
function flashWarning(msg, delay) { return flashShow(msg, 'warning', delay); }
function flashShow(msg, type, delay) { var tmp_id = Math.floor(Math.random()*11); if(delay===undefined) { delay = 3000; } $(".flash-message").empty(); $('.flash-message:first').append('<div id="alert-id-'+tmp_id+'" class="alert alert-block alert-'+type+'"><button class="close" data-dismiss="alert" type="button">×</button>'+msg+'</div><script type="text/javascript">if('+delay+' > 0) { $("#alert-id-'+tmp_id+'").hide().fadeIn(500).delay('+delay+').fadeOut(500); } else { $("#alert-id-'+tmp_id+'").hide().fadeIn(500); }</script>'); }
function flashClear() { $(".flash-message").fadeOut(500).empty(); }

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

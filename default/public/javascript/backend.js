var prevHash = false;

/**
 * Mensajes tipo Flash
 */
function flashInfo(msg) { return flashShow(msg, 'info'); }
function flashError(msg) { return flashShow(msg, 'danger'); }
function flashValid(msg) { return flashShow(msg, 'success'); }
function flashWarning(msg) { return flashShow(msg, 'warning'); }
function flashShow(msg, type) { $(".flash-message").empty(); $('.flash-message:first').append('<div class="alert alert-block alert-'+type+'"><button class="close" data-dismiss="alert" type="button">×</button>'+msg+'</div>'); }
function flashClear() { $(".flash-message").empty(); }

/** Buttons forward y back **/
$(function() { $("body").on('click', '.btn-back', function(event) { history.back();}); $("body").on('click', '.btn-forward', function(event) { history.forward();});   });

$("body").ajaxStart(function() { $("body").css("cursor", "wait");}).ajaxStop(function() { $("body").css("cursor", "default"); });

$(function() {
    $('body').on('click', 'button.disabled', function(e) {
        e.preventDefault();
        return;
    });   
    /*
    $('body').on('click', '.js-link', function(e) {
        e.preventDefault();
        var este = $(this);
        if(este.hasClass('no-ajax')) {
            if(este.attr('href') != '#' && este.attr('href') != '#/' && este.attr('href') != '#!/') {
                location.href = ""+este.attr('href')+"";
            }
        }
        if(este.hasClass('no-load') || este.hasClass('js-confirm')) {
            return false;
        }
        var val = true;
        var capa        = (este.attr('data-to')!=undefined) ? este.attr('data-to') : 'shell-content';
        var spinner     = este.hasClass('js-spinner') ? true : false;
        var change_url  = este.hasClass('js-url') ? true : false;
        var message     = true;
        var url         = este.attr('href');
        var before_load = este.attr('before-load');//Callback antes de enviar
        var after_load  = este.attr('after-load');//Callback después de enviar
        if(before_load!=null) {
            try { val = eval(before_load); } catch(e) { }
        }
        if(val) {
            prevHash = true;//Por si se utiliza el hashbang
            //@TODO Revisar la seguridad acá
            if(url!=$.KumbiaPHP.publicPath+'#' && url!=$.KumbiaPHP.publicPath+'#/' && url!='#' && url!='#/') {
                options = { capa: capa, spinner: spinner, msg: message, url: url, change_url: change_url};
                if($.kload(options)) {
                    if(after_load!=null) {
                        try { eval(after_load); } catch(e) { }
                    }
                }
            }
        }
        return true;
    });
    */
});


/**
 * Funciones para limpiar caracteres al igual que el trim de php
 */
function ltrim(str, opt) { if(opt) { while (str.charAt(0) == opt) str = str.substr(1, str.length - 1); } else { while (str.charAt(0) == " ") str = str.substr(1, str.length - 1); } return str; }
function rtrim(str, opt) { if(opt) { while (str.charAt(str.length - 1) == opt) str = str.substr(0, str.length - 1); } else { while (str.charAt(str.length - 1) == " ") str = str.substr(0, str.length - 1); } return str; }
function trim(str, opt) { var str = new String(str); return rtrim(ltrim(str, opt), opt); }
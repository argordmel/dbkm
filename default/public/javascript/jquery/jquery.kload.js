/**
 * Descripcion: Plugin de jquery para obtener elementos elementos y cargarlos en un container
 *
 * @category
 * @package     Javascript
 * @author      argordmel@gmail.com 
 */
(function($) {
    /**
     *
     * Opciones por defecto
     */
    var defaults = {
        change_url       : true, //Indica si cambia la url por la url de la petición
        async            : false, //Indica si la petición es asíncrona
        timeout          : 45000, //Tiempo de espera
        spinner          : true, //Indica si muestra el sppiner al cargar
        append_data      : false, //Indica si carga con html o append la data
        msj              : true, //Indica si muestra alertas
        response         : 'html', //Método de respuesta que se espera
        capa            : 'shell-content', //Capa a actualizar
        method          : 'GET', //Método a utilizar
        data            : null  //Data o parámetros a enviar
    };

    /**
     * Objeto para el load
     */
    $.kload = function(options) {
        //Variable de éxito (solo para peticiones no asíncronas)
        var request = false;
        //Extiendo las opciones
        var opt = $.extend(true, defaults, options);

        //Verifico si muestra el spiner
        if(opt.spinner==true){
            jsSpinner('show');
        }

        //Realizo la petición
        $.ajax({
            type: opt.method, url: opt.url, timeout: opt.timeout, async: opt.async, dataType: opt.response, data: opt.data,
            beforeSend: function(data) {
                $("[rel=tooltip]").tooltip('hide');
            },
            error: function (xhr, text, err) {
                var response = xhr.statusCode().status+" "+xhr.statusCode().statusText;
                if(opt.msg==true) {
                    try {
                        $('#error-ajax').html(response);
                        if($('#info-error-ajax').size() > 0) {
                             $('#info-error-ajax').html(xhr.responseText);
                        }
                        errorAjax();
                    } catch(e) {
                        alert('Oops! Se ha producido un error en la carga\nDetalle del error: '+response);
                    }
                }
                request = false;
            }
        }).success(function() {
            if(opt.change_url == true) {
                updateUrl(opt.url);
            }
        }).done(function(data) {
            if(opt.response == 'html') {
                //Verifico si carga la data o la adhiere
                (opt.append_data==true) ? $("#"+opt.capa).append(data) : $("#"+opt.capa).html(data);
                $("[rel=tooltip]").tooltip();                
                if(opt.capa == 'shell-content') {
                    $("html, body").animate({scrollTop: 0}, 500);
                }
                request = true;
                // Enlazar DatePicker
                $.KumbiaPHP.bindDatePicker();
                // Enlazar Uploads
                $.KumbiaPHP.bindFileUpload();                
                //Validate
                $.validateForm.initialize();
            } else {
                request = data;
            }
        });

        //Oculto el spinner si está habilitado
        if(opt.spinner==true) {
            jsSpinner('hide');
        }

        //Reestablesco las opciones iniciales
        defaults.response = 'html';
        defaults.method = 'GET';
        defaults.data = null;
        defaults.spinner = true;

        //Retorno la variable de éxito
        //Si la petición no es asíncrona retornará un boolean o el tipo de respuesta indicado (json)
        return request;
    };
})(jQuery);


/** Muestra/Oculta el spinner **/
function jsSpinner(action, target) {
    if(target==null) {
        target='spinner';
    }
    if(action=='show') {
        $("#spinner").attr('style','top: 50%; left:50%; margin-left:-50px; margin-top:-50px;');
        $("#shell-load").addClass('spinner-blur');
        $("#loading-content").show();
        $("#"+target).show().spin('large', 'white');
    } else {
        $("#loading-content").hide();
        $("#shell-load").removeClass('spinner-blur');
        $("#"+target).hide().spin(false);
    }
}

/**
* Función que actualiza la url con popstate, hashbang o normal
*/
function updateUrl(url) {
    /** Se quita el public path de la url */
    if($.KumbiaPHP.publicPath != '/') {
        url = url.split($.KumbiaPHP.publicPath);
        url = (url.length > 1) ? url[1] : url[0];
    } else  {
        url = ltrim(url, '/');
    }
    if(typeof window.history.pushState == 'function') {
        url = $.KumbiaPHP.publicPath+url;
        history.pushState({ path: url }, url, url);
    } else {
        window.location.hash = "#!/"+url;
    }
    return true;
}

/**
 * Función que cambia la url, si el navegador lo soporta
 */
function pushState(){
    // Función para enlazar cuando cambia la url de la página.
    $(window).bind('popstate', function(event) {
        if (!event.originalEvent.state)//Para Chrome
            return;
        $.kload({url: location.pathname});
    });
}

/**
 * Función que verifica el hash, se utiliza cuando no soporta el popstate
 */
function checkHash(){
    var direccion = ""+window.location+"";
    var nombre = direccion.split("#!/");
    if(nombre.length > 1){
        direccion = '/'+ltrim(nombre[1], '/');
        $.kload({url: direccion});
    }
}
/**
 * Función que cambia actualiza el content cuando cambia el hash
 */
function hashChange() {
    // Función para determinar cuando cambia el hash de la página.
    $(window).bind("hashchange",function(event) {
        if(prevHash) {
            prevHash = false;
            return;
        }
        var hash = ""+window.location.hash+"";
        hash = hash.replace("#!/","");
        if(hash && hash!="") {
            $.kload({url: hash});
        }
    });
}

/** Enlazo la url **/
$(document).ready(function() {
    if (typeof window.history.pushState == 'function') {
        pushState();
    } else {
        checkHash(); hashChange();
    }
});
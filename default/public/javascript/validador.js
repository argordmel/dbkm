/*
 * jQuery.bind-first library v0.2.3
 * Copyright (c) 2013 Vladimir Zhuravlev
 *
 * Released under MIT License
 * @license
 *
 * Date: Thu Feb  6 10:13:59 ICT 2014
 **/
(function(t){function e(e){return u?e.data("events"):t._data(e[0]).events}function n(t,n,r){var i=e(t),a=i[n];if(!u){var s=r?a.splice(a.delegateCount-1,1)[0]:a.pop();return a.splice(r?0:a.delegateCount||0,0,s),void 0}r?i.live.unshift(i.live.pop()):a.unshift(a.pop())}function r(e,r,i){var a=r.split(/\s+/);e.each(function(){for(var e=0;a.length>e;++e){var r=t.trim(a[e]).match(/[^\.]+/i)[0];n(t(this),r,i)}})}function i(e){t.fn[e+"First"]=function(){var n=t.makeArray(arguments),i=n.shift();return i&&(t.fn[e].apply(this,arguments),r(this,i)),this}}var a=t.fn.jquery.split("."),s=parseInt(a[0]),f=parseInt(a[1]),u=1>s||1==s&&7>f;i("bind"),i("one"),t.fn.delegateFirst=function(){var e=t.makeArray(arguments),n=e[1];return n&&(e.splice(0,2),t.fn.delegate.apply(this,arguments),r(this,n,!0)),this},t.fn.liveFirst=function(){var e=t.makeArray(arguments);return e.unshift(this.selector),t.fn.delegateFirst.apply(t(document),e),this},u||(t.fn.onFirst=function(e,n){var i=t(this),a="string"==typeof n;if(t.fn.on.apply(i,arguments),"object"==typeof e)for(type in e)e.hasOwnProperty(type)&&r(i,type,a);else"string"==typeof e&&r(i,e,a);return i})})(jQuery);


/**
 * Plugin para validar formularios
 *  
 * Para validar un formulario indicar la clase js-validate y el attributo novalidate=novalidate
 * Para evitar que valide con un blur el campo indicar el atributo live-validate=false
 * Clases para los campos       input-required
 *                              input-alphanum
 *                              input-list
 *                              input-numeric
 *                              input-int
 *                              input-email
 *                              input-phone
 * Atributos adicionales        data-equalto="id_campo_a_comparar"
 *                              minlength="número mínimo de dígitos"
 *                              maxlength="número máximo de dígitos"
 *                               
 */


/**
 * Variable que contiene los tipos de validadores
 * @type Array
 */
var validators = ["input-required", "input-alphanum", "input-list", "input-numeric", "input-int", "input-email", "input-phone", "input-date"];

/**
 * Método para aplicar las validaciones de
 * @param JQueryObject input
 * @returns mixed
 */
function validateInput(input) {
    if(input.attr('data-equalto') !== undefined) {
        if(!equalTo(input, input.attr('data-equalto'))) {
            return false;
        }
    }    
    if(input.attr('minlength') !== undefined || input.attr('maxlength') !== undefined) {     
        if(!inputLimit(input)) {
            return false;
        }
    }    
    if(input.hasClass('input-required')) {        
        if(!inputRequired(input)) {
            return false;
        }
    }
    var clases = input.attr('class').split(' ');    
    for(c = 0 ; c < clases.length ; c++) {        
        if($.inArray(clases[c], validators) >= 0 && clases[c] !== 'input-required') {
            tmp = clases[c].split('-');
            if(tmp[1] === undefined || tmp[1] === null || tmp[1] === '') {
                continue;
            }            
            name = tmp[1].substr(0,1).toUpperCase()+tmp[1].substr(1,tmp[1].length).toLowerCase();                
            fn = 'input'+name+'(input)';                
            eval(fn);
        }
    }    
}

/**
 * Eventos para validar al hacer un blur al campo 
 */
$('body').on('blur', 'form.js-validate input, textarea, select, checkbox, radio', function(e) {
    var este = $(this);
    if(este.parents('form:first').attr('live-validate') === false) {
        return true;
    } else {
        validateInput(este);
    }
});

/**
 * Eventos para validar al hacer un keypress al campo 
 */
$('body').onFirst('keyup', 'form.js-validate input, textarea', function(e) {
    var este = $(this);
    if($(this).hasClass('input-required')) {
        validateInput(este);
    }
});

/**
 * Eventos para validar al cambiar un select
 */
$('body').onFirst('change', 'form.js-validate select', function(e) {    
    var este = $(this);
    if($(this).hasClass('input-required')) {
        validateInput(este);
    }
});

/**
 * Evento para validar un formulario al enviarlo.
 */
$('body').onFirst('submit', 'form.js-validate', function(e) {
    e.preventDefault();
    var cont = 0;
    $(this).find(":input").each(function(e) {        
        if($(this).attr('data-invalid') !== undefined) {
            cont++;
        } else {            
            validateInput($(this));
            if($(this).attr('data-invalid') !== undefined) {
                cont++;
            }
        } 
    });        
    if(cont > 0) {
        e.stopImmediatePropagation();
        return false;
    }
    return true;    
});

/**
 * Método para obtener el mensaje predeterminado del input
 * @param JQueryObject input
 * @param string msg
 * @returns string
 */
function getInputMessage(input, msg) {
    return (input.attr('validate-msg') !== undefined) ? input.attr('validate-msg') : msg;
}

/**
 * Método para mostrar un input con error
 * @param JQueryObject input
 * @param string msg
 * @returns boolean
 */
function showInputError(input, msg) {
    if(input.attr('data-invalid') !== undefined) {
        return false;
    }
    input.attr('data-invalid', '');
    var input_container = input.parent();    
    if(input_container.hasClass('input-group')) {
        input_container = input_container.parent();
    }
    input_container.addClass('has-error');
    input_container.find('.help-error').text(msg);
    return false;
}

/**
 * Método para quitar el error de n input
 * @param JQueryObject input
 * @returns {Boolean}
 */
function removeInputError(input) {
    input.removeAttr('data-invalid');
    var input_container = input.parent();  
    if(input_container.hasClass('input-group')) {
        input_container = input_container.parent();
    }    
    input_container.removeClass('has-error');
    return true;
}


/****************************************************
 * 
 * CUSTOMIZED VALIDATORS
 * 
 ****************************************************/

/**
 * Función para validar campos requeridos
 * @param JQueryObject input
 * @returns boolean
 */
function inputRequired(input) { 
    if(input.is('select')) {
        return inputList(input);
    }
    var v_msg = getInputMessage(input, 'Por favor completa este campo');
    if (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val()) ) { 
        return showInputError(input, v_msg);
    }
    return removeInputError(input);
}


/**
 * Función para validar campos alfanuméricos
 * @param JQueryObject input
 * @returns boolean
 */
function inputAlphanum(input) {
    var v_msg = getInputMessage(input, 'Ingresa solo valores alfanuméricos');
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if (!(/^[a-zA-Z0-9-ZüñÑáéíóúÁÉÍÓÚÜ._\s]+$/.test(input.val()))) {            
            return showInputError(input, v_msg);            
        }
    }        
    return removeInputError(input);
}

/**
 * Función para validar un elemento de una lista
 * 
 * @param JQueryObject input
 * @returns Boolean
 */
function inputList(input) {
    var v_msg = getInputMessage(input, 'Selecciona un elemento de la lista');
    if ( (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        return showInputError(input, v_msg);
    }
    return removeInputError(input);
}

/**
 * Función para indicar que el valor de un campo sea igual a otro
 * @param JQueryObject input
 * @param string target
 * @returns Boolean
 */
function equalTo(input, target) {
    target = $("#"+target);
    if(target.size() === 0) {
        return showInputError(input, 'No se ha podido establecer el campo a comparar');
    }
    removeInputError(input);
    if(target.val() !== input.val()) {
        return showInputError(input, 'El campo no coincide');
    } 
    return removeInputError(input);
}

/**
 * Función para validar campos numéricos
 * @param JQueryObject input
 * @returns boolean
 */
function inputNumeric(input) {    
    var v_msg = getInputMessage(input, 'Ingresa solo valores numéricos');
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if (!(/^[-]?\d+(\.\d+)?$/.test(input.val()))) {                        
            return showInputError(input, v_msg);            
        }
    }        
    return removeInputError(input);
}

/**
 * Función para validar campos enteros
 * @param JQueryObject input
 * @returns boolean
 */
function inputInt(input) {
    var v_msg = getInputMessage(input, 'Ingresa solo números enteros');
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if (!(/^\d+$/.test(input.val()))) {            
            return showInputError(input, v_msg);            
        }
    }        
    return removeInputError(input);
}

/**
 * Función para validar email
 * @param JQueryObject input
 * @returns boolean
 */
function inputEmail(input) {
    var v_msg = getInputMessage(input, 'Ingresa un email válido');
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if (!(/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(input.val()))) { 
            return showInputError(input, v_msg);            
        }
    }        
    return removeInputError(input);
}

/**
 * Función para validar el número de teléfono
 * @param JQueryObject input
 * @returns boolean
 */
function inputPhone(input) {
    var limiteMenor = 7;
    var limiteMayor = 10;
    var v_msg = getInputMessage(input, 'El número debe tener '+limiteMenor+' o '+limiteMayor+' dígitos');
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if(!inputNumeric(input)) {
            return false;
        }
        input.attr('data-msg', v_msg);
        input.attr('minlength', limiteMenor);
        input.attr('maxlength', limiteMayor);
        if(!inputLimit(input)) {
            return false;
        }               
    }        
    return removeInputError(input);
}

/**
 * Función para validar el número de dígitos
 * @param JQueryObject input
 * @returns boolean
 */
function inputLimit(input) {
    
    var limiteMenor = (input.attr('minlength') !== undefined) ? input.attr('minlength') : 0;
    var limiteMayor = (input.attr('maxlength') !== undefined) ? input.attr('maxlength') : 0;
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        if(limiteMenor > 0 && limiteMayor === 0) {
            var v_msg = getInputMessage(input, 'El campo debe tener mínimo '+limiteMenor+' dígito(s)');
            if ( input.val().length < limiteMenor ) {
                return showInputError(input, v_msg);            
            }
        } else if(limiteMenor === 0 && limiteMayor > 0) {
            var v_msg = getInputMessage(input, 'El campo debe tener máximo '+limiteMayor+' dígito(s)');
            if ( input.val().length > limiteMayor ) {
                return showInputError(input, v_msg);            
            }
        } else {
            var v_msg = getInputMessage(input, 'El campo debe tener '+limiteMenor+' o '+limiteMayor+' dígitos');        
            if ( (input.val().length < limiteMenor) || (input.val().length > limiteMayor)) {
                return showInputError(input, v_msg);            
            }
        }    
    }
    return removeInputError(input);        
    
}

/**
 * Función para validar la fecha 
 * @param JQueryObject input
 * @returns boolean
 */
function inputDate(input) {
    var v_msg = getInputMessage(input, 'Fecha incorrecta');
    var f_parts;
    if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) { 
        f_parts = input.val().split('-');
        if(f_parts.length == 3) {
            anno    = f_parts[0];
            mes     = f_parts[1];
            dia     = f_parts[2];
            if(anno.length !== 4 || mes.length !== 2 || dia.length !== 2) {
                return showInputError(input, v_msg);                
            }        
            if (! (/^(0[1-9]|1[0-9]|2[0-8]|29((?=-([0][13-9]|1[0-2])|(?=-(0[1-9]|1[0-2])-([0-9]{2}(0[48]|[13579][26]|[2468][048])|([02468][048]|[13579][26])00))))|30(?=-(0[13-9]|1[0-2]))|31(?=-(0[13578]|1[02])))-(0[1-9]|1[0-2])-[0-9]{4}$/.test(dia+'-'+mes+'-'+anno))) {            
                return showInputError(input, v_msg);
            }
            return true;
        } else {
            return showInputError(input, v_msg);
        }                  
    }        
    return removeInputError(input);
}


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

(function($) {
    
    /**
     * Objeto del validador
     */
    $.validateForm = {                
        
        /**
         * Clases para validar los input
         *
         * @var Array
         */
        validators: [
            "input-required",             
            "input-alpha",
            "input-alphanum",
            "input-date", 
            "input-numeric", 
            "input-integer",
            "input-email",
            "input-url",
            "input-domain",
            "input-time",
            "input-color"            
        ],
        
        patterns : {
            //abc
            alpha:          /[a-záéíóúüñ_]/i,
            //abc123
            alphanum:       /[a-záéíóúüñ0-9_]/i,
            //123
            integer:        /^[-+]?\d+$/,            
            //1234.99
            numeric:        /^[-+]?\d+(\.\d+)?$/,  
            //2,456.99
            money:          /^[-+]?\d{1,3}(,\d{3})*(\.\d\d)?$/,
            //user@example.com
            email :         /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            //http://example.com
            url:            /(https?|ftp|file|ssh):\/\/(((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?/i,
            //example.com
            domain:         /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/i,
            // DD-MM-YYYY
            date:           /^(0[1-9]|1[0-9]|2[0-8]|29((?=-([0][13-9]|1[0-2])|(?=-(0[1-9]|1[0-2])-([0-9]{2}(0[48]|[13579][26]|[2468][048])|([02468][048]|[13579][26])00))))|30(?=-(0[13-9]|1[0-2]))|31(?=-(0[13578]|1[02])))-(0[1-9]|1[0-2])-[0-9]{4}$/,
            // HH:MM:SS
            time :          /(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}/,
            // #FFF o #FFFFFF
            color:          /^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/i
        },
        
        initialize: function() {
            $(function() {                
                /**
                 * Cuando se envía un formulario 
                 */
                $('form.js-validate').onFirst('submit', function(e) {
                    if($.validateForm.run($(this)) !== true) {                        
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }                    
                    return true;
                }).on('blur', 'input, textarea, select, checkbox, radio', function(e) {                   
                    if($(this).parents('form:first').attr('js-validate-nolive') === undefined) {                                                
                        $.validateForm.checkInput($(this));
                    }
                })
                
                /**
                 * Eventos para validar al cambiar un select, un radio o checkbox
                 */
                $('body').onFirst('change', 'form.js-validate select, form.js-validate radio, form.js-validate checkbox', function(e) { 
                    if($(this).hasClass('input-required')) {
                        setTimeout(function() {
                            $.validateForm.checkInput($(this));
                        }, 500);
                    }
                });;
                
                /**
                 * Eventos para validar al hacer un keypress al campo 
                 */
                $('body').onFirst('keyup', 'form.js-validate input, form.js-validate textarea', function(e) {                    
                    if($(this).parents('form:first').attr('js-validate-nolive') === undefined) {
                        var input = $(this);
                        if(input.hasClass('input-required')) {
                            setTimeout(function() {
                                $.validateForm.checkInput(input);
                            }, 1000);
                        }
                    }
                });
                
                /**
                 * Cuando cambia de fecha con el datepicker
                 */                
                $("body").on("dp.change", '.datepicker', function (e) {
                    var este = $(this);
                    input = este.find(':input');                                        
                    setTimeout(function() {
                        $.validateForm.checkInput(input, e);
                    }, 500);                                            
                }); 
                
                /**
                 * Cuando cambia de fecha manualmente
                 */
                $("body").on("change", 'form.js-validate .input-date', function (e) {            
                    input = $(this);
                    setTimeout(function() {                        
                        $.validateForm.checkInput(input, e);                        
                    }, 500);
                });               
                               
            });
        },
        
        run: function(form) {
            if(!form.is('form')) {
                console.log('No se ha podido establecer el formulario.');
                return false;
            }
            var cont = 0;
            form.find(":input").each(function(e) { 
                if($.validateForm.checkInput($(this)) !== true) {
                    console.log('Error: '+$(this).attr('name'));
                    cont++;
                }                
            });                        
            if(cont > 0) {    
                form.find('[data-invalid=""]:first').focus();
                return false;
            }
            return true;
        },
        
        checkInput: function(input, event) {
            $.validateForm.removeError(input);
            if(input.hasClass('input-required')) {        
                if($.validateForm.required(input) !== true) {            
                    return false;
                }
            }
            if(input.attr('data-equalto') !== undefined) {
                if($.validateForm.equalTo(input, input.attr('data-equalto')) !== true) {
                    return false;
                }
            }
            if(input.attr('minlength') !== undefined || input.attr('maxlength') !== undefined) {     
                if($.validateForm.limit(input) !== true) {
                    return false;
                }
            }
            
            if(input.attr('class') !== undefined) {
                var clases = input.attr('class').split(' ');    
                for(c = 0 ; c < clases.length ; c++) {                    
                    if($.inArray(clases[c], $.validateForm.validators) >= 0 && clases[c] !== 'input-required') {
                        tmp = clases[c].split('-');
                        if(tmp[1] === undefined || tmp[1] === null || tmp[1] === '') {
                            continue;
                        }            
                        name = tmp[1].toLowerCase();                                    
                        fn = '$.validateForm.'+name+'(input)';                    
                        if(eval(fn) !== true) {
                            return false;
                        }
                    }
                } 
            }
            
            if( ( input.hasClass('input-checkin') || input.hasClass('input-checkout') )  && input.hasClass('input-date')) {
                if($.validateForm.dateRange(input, event) !== true) {
                    return false;
                }                           
            }
            
            return true;
            
        },                
                
        message: function(input, msg) {
            return (input.attr('validate-msg') !== undefined) ? input.attr('validate-msg') : msg;
        },
        
        showError: function(input, msg) {
            if(input.attr('data-invalid') !== undefined) {
                return false;
            }
            input.attr('data-invalid', '');
            var input_container = input.parent();    
            if(input_container.hasClass('input-group') || input_container.is('label')) {
                input_container = input_container.parent();
            }
            input_container.addClass('has-error');
            input_container.find('.help-error').text(msg);
            return false;
        },
               
        removeError: function(input) {        
            input.removeAttr('data-invalid');
            var input_container = input.parent();  
            if(input_container.hasClass('input-group') || input_container.is('label')) {
                input_container = input_container.parent();
            }    
            input_container.removeClass('has-error');
            return true;
        },
        
        
        /************
         * 
         * VALIDATORS         
         * 
         ************/
        
        required: function(input) {            
            if(input.is('select')) {
                var v_msg = $.validateForm.message(input, 'Selecciona un elemento de la lista');
                if (input.val().length === 0) { 
                   return $.validateForm.showError(input, v_msg);
                }
            } else if(input.is(':checkbox')) {         
                var v_msg = $.validateForm.message(input, 'Selecciona algún elemento.');
                if(!(input.is(':checked'))) {        
                    return $.validateForm.showError(input, v_msg);
                }
            } else if(input.is(':radio')) {
                var v_msg = $.validateForm.message(input, 'Selecciona alguna opción.');
                var field = input.attr('name');
                if(!($('input[name="'+field+'"]').is(':checked'))) {
                    return $.validateForm.showError(input, v_msg);
                }
            } else {
                var v_msg = $.validateForm.message(input, 'Por favor completa este campo');                
                if (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val()) ) { 
                    return $.validateForm.showError(input, v_msg);
                }
            }                                                    
            return $.validateForm.removeError(input);
        },
        
        equalTo: function(input, target) {
            var v_msg = $.validateForm.message(input, 'El campo no coincide');
            target = $("#"+target);
            if(target.size() === 0) {
                return $.validateForm.showError(input, 'No se ha podido establecer el campo a comparar');
            }    
            if(target.val() !== input.val()) {
                return $.validateForm.showError(input, v_msg);
            } 
            return $.validateForm.removeError(input);
        },
        
        limit: function(input) {
            var min = (input.attr('minlength') !== undefined) ? input.attr('minlength') : 0;
            var max = (input.attr('maxlength') !== undefined) ? input.attr('maxlength') : 0;
            var v_msg;
            if (! (input.val() === null || input.val().length === 0 || /^\s+$/.test(input.val())) ) {
                if(min > 0 && max === 0) {            
                    if ( input.val().length < min ) {
                        v_msg = $.validateForm.message(input, 'El campo debe tener mínimo '+min+' dígito(s)');
                        return $.validateForm.showError(input, v_msg);            
                    }
                } else if(min === 0 && max > 0) {
                    if ( input.val().length > max ) {
                        v_msg = $.validateForm.message(input, 'El campo debe tener máximo '+max+' dígito(s)');
                        return $.validateForm.showError(input, v_msg);            
                    }
                } else {
                    if ( (input.val().length < min) || (input.val().length > max)) {
                        v_msg = $.validateForm.message(input, 'El campo debe tener '+min+' o '+max+' dígitos');                    
                        return $.validateForm.showError(input, v_msg);            
                    }
                }            
            }
            return $.validateForm.removeError(input);            
        },
        
        alpha: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa solo valores alfabéticos');
            return $.validateForm.pattern(input, this.patterns.alpha, v_msg);            
        },
        
        alphanum: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa solo valores alfanuméricos');
            return $.validateForm.pattern(input, this.patterns.alphanum, v_msg);            
        },
        
        numeric: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa solo valores numéricos');
            return $.validateForm.pattern(input, this.patterns.numeric, v_msg);            
        },
        
        integer: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa solo números enteros');
            return $.validateForm.pattern(input, this.patterns.integer, v_msg);            
        },
        
        money: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa un valor correcto');
            return $.validateForm.pattern(input, this.patterns.money, v_msg);            
        },
        
        email: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa un email correcto');
            return $.validateForm.pattern(input, this.patterns.email, v_msg);            
        },
        
        url: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa una url válida');
            return $.validateForm.pattern(input, this.patterns.url, v_msg);            
        },
        
        domain: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa una dirección válida');
            return $.validateForm.pattern(input, this.patterns.domain, v_msg);            
        },
        
        time: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa una hora válida');
            return $.validateForm.pattern(input, this.patterns.time, v_msg);            
        },
        
        color: function(input) {             
            var v_msg = $.validateForm.message(input, 'Ingresa un color válido');
            return $.validateForm.pattern(input, this.patterns.color, v_msg);            
        },
        
        date: function(input) {            
            var v_msg   = $.validateForm.message(input, 'Fecha incorrecta');
            if(input.val().length > 0) {                
                var f_parts = input.val().split('-');            
                if(f_parts.length !== 3) {               
                    return $.validateForm.showError(input, v_msg);
                }
                d1 = f_parts[0];
                d2 = f_parts[1];
                d3 = f_parts[2];            
                if(d1.length === 4 && d2.length === 2 && d3.length === 2) { //YYYY-MM-DD                
                    return $.validateForm.pattern(input, this.patterns.date, v_msg, d3+'-'+d2+'-'+d1);
                } else if(d3.length === 4 && d2.length === 2 && d1.length === 2) { //DD-MM-YYYY
                    return $.validateForm.pattern(input, this.patterns.date, v_msg);
                } else {
                    return $.validateForm.showError(input, v_msg);
                }
            }
            return $.validateForm.removeError(input);
        },
        
        dateRange: function(input, event) {
            
            if(input.attr('data-invalid') === undefined && input.val().length > 0) {              
                
                var container = input.parents('.row:first');                
                
                if(input.hasClass('input-checkin')) {                                       
                    var input_checkin   = input;
                    var input_checkout  = container.find('.input-checkout');
                    if(input_checkout.size() > 0) {
                        if(event !== undefined && event.date !== undefined) {                            
                            input_checkout.parent().data("DateTimePicker").setMinDate(event.date);
                        }
                        if(input_checkout.val().length > 0) {
                            if(input_checkin.val() > input_checkout.val()) {
                                return $.validateForm.showError(input, 'La fecha inicial no puede ser mayor que la fecha final');
                            }
                        }
                    }                    
                } else if(input.hasClass('input-checkout')) {
                    var input_checkout = input;
                    var input_checkin  = container.find('.input-checkin');                            
                    if(input_checkin.size() > 0) {                                    
                        if(event !== undefined && event.date !== undefined) {                            
                            input_checkin.parent().data("DateTimePicker").setMaxDate(event.date);                
                        }
                        if(input_checkin.val().length > 0) {
                            if(input_checkout.val() < input_checkin.val()) {       
                                return $.validateForm.showError(input, 'La fecha final no puede ser menor que la fecha inicial');
                            }
                        }
                    }                   
                }

            }            
            return $.validateForm.removeError(input);
        },
        
        pattern: function(input, pattern, msg, custom_val) {
            var val = (custom_val !== undefined) ? custom_val : input.val();
            if(val.length > 0) {
                if (!(pattern.test(val))) {
                    return $.validateForm.showError(input, msg);                       
                }                
            }
            return $.validateForm.removeError(input);
        }
        
    }
    
    $.validateForm.initialize();
    
})(jQuery);
(function($){

    var methods = {

        init : function( options ) {
                    //Defino unas opciones por defecto
                    var opt = {                        
                        form_show       : false, //Indica si muestra o no el formulario
                        form_to         : '', //Url para el envío del form
                        form_data       : false, //campo y valor predeterminado [campo, valor]
                        form_attr       : 'class="js-remote form-search"', //Attributos para el form
                        form_container  : 'shell-content', //Contenedor del formulario
                        
                        order_to        : '', //Url para el ordenamiento
                        order_attr      : 'class="js-link js-data-order js-spinner"', //Atributos para los link de ordenamiento
                        order_container : 'shell-content', //Contendeor a cargar
                        
                        col_hidden      : 'col-hidden' //Clase para indicar las columnas que se pueden ocultar                         
                    }

                    $.extend(opt, options); //Extiende las opciones recibidas con las default
                    
                    if(opt.form_to !== '') {                        
                        opt.form_show = true;
                    }
                    
                    return this.each(function(k) {
                        var table = $(this); //Se toma la tabla en objeto de jquery
                        var thead = table.find("thead"); //Se busca el header de la tabla
                        var tbody = table.find("tbody"); //Se busca el body de la tabla
                        var hdrCols = thead.find('th'); //Se busca los títulos de las columnas
                        var bdyRows = tbody.find('tr'); //Se busca las filas de las columnas
                        var container; //Contenedor de todo el resultado del script
                        container = table.parents('.'+opt.container+':first');
                        
                        //Verifico si la tabla está dentro del container overflow
                        if(table.parent().hasClass('container-overflow')) { 
                            container = table.parent().prev().hasClass('btn-toolbar') ? table.parent().prev() : $('<div class="btn-toolbar btn-toolbar-top"></div>');
                        } else {
                            container = table.prev().hasClass('btn-toolbar') ? table.prev() : $('<div class="btn-toolbar btn-toolbar-top"></div>');
                        }
                                                                        
                        var hiddenCol = $('<div class="pull-right"><div class="btn-group"><button class="btn btn-default btn-only dropdown-toggle" data-toggle="dropdown"><span class="hidden-xs"> COLUMNAS <i class="caret"></i></span><span class="visible-xs"><i class="fa fa-th"></i></span></button><ul class="dropdown-menu pull-right" /></div></div>');
                        
                        //Variable para almacenar las columnas responsivas
                        var th_responsive = [];
                        
                        hdrCols.each(function(i) {
                            var th = $(this), cls = th.attr("class");
                            //Se asigna un id a la colomna definida en el thead
                            var id = "col-"+k+"-"+ i;
                            th.attr("id", id);
                            
                            //Verifico si la columna no está bloqueada
                            if(!th.hasClass('col-blocked') && table.hasClass('table-responsive')) {
                                th_responsive.push('td:nth-of-type('+(i+1)+'):before { content: "'+th.text()+': "; }');
                            }
                            
                            // Hay que revisar los colspan
                            bdyRows.each(function(){
                                var cell = $(this).find("th, td").eq(i);
                                cell.attr("headers", id);
                                if (cls) { 
                                    cell.addClass(cls); 
                                };
                                //Creo la opción para seleccionar el orden para los móviles
                                if(opt.order_to != '') {
                                    if(th.attr('data-order')!=undefined) {
                                        text    = th.text();
                                        order   = th.attr('data-order');
                                        while (opt.order_to.charAt(opt.order_to.length - 1) == '/') 
                                            opt.order_to = opt.order_to.substr(0, opt.order_to.length - 1);                                     
                                        asc     = opt.order_to+'/order.'+order+'.asc/';
                                        desc    = opt.order_to+'/order.'+order+'.desc/';
                                        cell.append('<div class="btn-group visible-xs"><a class="" data-toggle="dropdown" href="#"><span class="caret"></span></a><ul class="dropdown-menu pull-right"><li><a href="'+asc+'" '+opt.order_attr+' data-div="'+opt.order_container+'"><i class="fa fa-caret-up fa-pd-expand"></i>Ascendente</a></li><li><a href="'+desc+'" '+opt.order_attr+' data-div="'+opt.order_container+'"><i class="fa fa-caret-down fa-pd-expand"></i>Descendente</a></li></ul></div>');
                                    }
                                }
                            });
                            
                            
                            // Creo las columnas para mostrar/ocultar según la clase del th en el head
                            if (opt.col_hidden && th.hasClass(opt.col_hidden) ) {
                                //Regisro las columnas
                                text = th.text();
                                text = text.substr(0,1).toUpperCase()+text.substr(1,text.length).toLowerCase();
                                                                
                                var toggle = $('<li><div class="checkbox" style="margin-left: 10px"><label style="font-size: 110%"><input type="checkbox" name="toggle-cols" id="toggle-col-'+i+'" value="'+id+'"> '+text+'</label></div></li>');
                                //Agrego las columnas que se pueden ocultar
                                hiddenCol.find("ul").append(toggle);
                                toggle.find("input").change(function(){
                                    var input = $(this),
                                    val = input.val(),
                                    cols = $("#" + val + ", [headers="+ val +"]", table);
                                    (input.is(":checked")) ? cols.removeClass('hidden').removeAttr('style') : cols.addClass('hidden').attr('style', 'display: none !important');
                                }).bind("updateCheck", function(){
                                    if (th.hasClass('hidden')) {
                                        $(this).attr("checked", false);
                                    } else {
                                        $(this).attr("checked", true);
                                    }
                                }).trigger("updateCheck");
                            }
                            
                            //Creo la opción para seleccionar el orden para desktop
                            if(opt.order_to!='') {
                                if(th.attr('data-order')!=undefined) {
                                    text    = th.text();
                                    order   = th.attr('data-order');
                                                                        
                                    while (opt.order_to.charAt(opt.order_to.length - 1) == '/') 
                                        opt.order_to = opt.order_to.substr(0, opt.order_to.length - 1);                                     
                                    
                                    asc     = opt.order_to+'/order.'+order+'.asc/';
                                    desc    = opt.order_to+'/order.'+order+'.desc/';
                                    th.html('<div class="btn-group hidden-xs"><a class="" data-toggle="dropdown" href="#">'+text+' <span class="caret"></span></a><ul class="dropdown-menu"><li><a href="'+asc+'" '+opt.order_attr+' data-div="'+opt.order_container+'"><i class="fa fa-caret-up fa-pd-expand"></i>Ascendente</a></li><li><a href="'+desc+'" '+opt.order_attr+' data-div="'+opt.order_container+'"><i class="fa fa-caret-down fa-pd-expand"></i>Descendente</a></li></ul></div>');
                                }
                            }                            
                            
                        });
                        
                        //Si hay columnas responsivas
                        if(th_responsive.length > 0) {
                            $('head').append('<style type="text/css">@media (max-width: 640px) { '+th_responsive.join('')+'}</style>');
                        }
                        
                        var containerForm;
                        var visible = (opt.form_data) ? '' : 'hidden';
                        
                        if(opt.form_show) {
                            if(opt.form_to==undefined || opt.form_to=='') {
                                alert('No se ha definido una url para la búsqueda para el datagrid');
                                exit();
                            }
                            
                            var select = '';

                            hdrCols.each(function(i) {
                                field = $(this).attr('data-search');
                                if(field != undefined) {
                                    text = field.replace('_', ' ').replace('_', ' ').toLowerCase();
                                    text = text.split('.');
                                    text = (text.length > 1) ? text[1] : text[0];
                                    text = text.substr(0,1).toUpperCase()+text.substr(1,text.length).toLowerCase();
                                    selected = (opt.form_data[0] != undefined && opt.form_data[0] == field) ? 'selected="selected"' : '';
                                    select = (select!='') ? select+'<option value="'+field+'" '+selected+'>'+text+'</option>' : '<option value="'+field+'">'+text+'</option>';
                                }
                            });

                            if(select=='') {
                                select = '<option value="">CUALQUIER CAMPO</option>';
                            }                            
                            value = (opt.form_data[1] != undefined) ? opt.form_data[1] : '';
                            containerForm = '<div class="row"><div class="form-search-container '+visible+'"><form action="'+opt.form_to+'" method="post" '+opt.form_attr+' data-to="'+opt.form_container+'" class="form-inline" role="form"><div class="row"><div class="col-xs-12 col-sm-3"><label class="sr-only" for="form_search_field">Campo</label><select id="form_search_field" class="form-control" required="required" name="field">'+select+'</select></div><div class="col-xs-12 col-sm-3"><label class="sr-only" for="form_search_value">Palabra o texto</label><input id="form_search_value" name="value" type="text" value="'+value+'" class="form-control" placeholder="Palabra o texto" required="required"/></div><div class="col-xs-12 col-sm-1"><button type="submit" class="btn btn-info"><i class="fa fa-share"></i></button></div></div></form></div></div>';                            

                        }
                        
                        if( (opt.col_hidden && (thead.find('.'+opt.col_hidden).length > 0) ) || opt.form_show) {
                            
                            (table.parent().hasClass('container-overflow')) ? table.parent().before(container) : table.before(container);
                            
                            if(container.find('.row:first').size() === 0) {
                                container.prepend('<div class="row"></div>');                                
                            }
                            
                            //Si hay alguna columna que se oculte
                            if(opt.col_hidden && (thead.find('.'+opt.col_hidden).length > 0) ) {
                                container.children('.row:first').prepend(hiddenCol);
                            }
                            
                            if(!container.find('.btn-actions').length) {
                                container.children('.row:first').append('<div class="btn-actions"></div>');
                            }                            
                            
                            container.append('<hr class="divider">');

                            if(opt.form_show) {                                  
                                container.find('.btn-actions').prepend('<button class="btn btn-info text-bold btn-form-search"><i class="btn-icon-only fa fa-search"></i> <span class="hidden-xs">BUSCAR</span></button>');
                                container.append(containerForm);
                                container.append('<hr class="divider '+visible+'">');
                            }
                            
                        }                        
                    });
                }
    };
    
    $.fn.kgrid = function(method){
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Este método ' +  method + ' no existe en $.kgrid' );
            return false;
        }
    };

})(jQuery);

$(function() {
    $('body').on('click', '.btn-toolbar-top .btn-form-search', function() {
        load_container = $(this).parents('.btn-toolbar:first').find('.form-search-container:first');        
        if(load_container.hasClass('hidden')) {
            load_container.removeClass('hidden').hide().fadeIn(250);
            load_container.parent().next('hr').removeClass('hidden').hide().fadeIn(250);
            load_container.find(':input:first').focus();
        } else {
            load_container.fadeOut(50).addClass('hidden');
            load_container.parent().next('hr').fadeOut(50).addClass('hidden');
        }
    });
    
    $('body').on('click', '.table-responsive tbody tr', function() {
        elem = $(this).find('td.btn-actions:first');
        action = (elem.is(':hidden')) ? 'show' : 'hidden';
        all = $(this).parent().find('td.btn-actions');
        all.each(function(){ if(!$(this).is(':hidden')) { $(this).hide(); } });
        (action==='show') ? elem.css('display','block') : elem.hide();        
    });
})
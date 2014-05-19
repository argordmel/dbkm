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

/*** Menú movil ***/
var toggler             = '.navbar-toggle';
var pagewrapper         = '#shell-load';
var navigationwrapper   = '.navbar-header';
var menuwidth           = '100%'; // the menu inside the slide menu itself
var slidewidth          = '80%';
var menuneg             = '-100%';
var slideneg            = '-80%';

$(function() {
    
    $('#slide-nav.navbar .container').append($('<div id="navbar-height-col"></div>'));
    $(".slide-navbar").height(function() {
        return window.innerHeight-100;
    });
    $("#slide-nav").on("click", toggler, function (e) {        
        var selected = $(this).hasClass('slide-active');
        $('#slidemenu').stop().animate({
            left: selected ? menuneg : '0px'
        });
        $('#navbar-height-col').stop().animate({
            left: selected ? slideneg : '0px'
        });
        $(pagewrapper).stop().animate({
            left: selected ? '0px' : slidewidth
        });
        $(navigationwrapper).stop().animate({
            left: selected ? '0px' : slidewidth
        });
        if(!selected) {
            $(this).css('margin-right', '50px');
        } else {
            $(this).removeAttr('style');
        }
        $(this).toggleClass('slide-active', !selected);
        $('#slidemenu').toggleClass('slide-active');
        $('#shell-load, .navbar, body, .navbar-header').toggleClass('slide-active');
        
        var selected = '#slidemenu, #shell-load, body, .navbar, .navbar-header';    
        $(window).on("resize", function () {              
            if ($(window).width() > 767 && $('.navbar-toggle').is(':hidden')) {                
                $(selected).removeClass('slide-active');
            }
        });
    });
    
    $('#slidemenu a.js-link').on('click', function(e) {                
        if(!$(this).hasClass('dropdown-toggle')) {                
            $('.navbar-toggle', "#slide-nav").click();            
        }
    });    
});

(function($){
    $(document).ready(function(){
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
            event.preventDefault(); 
            event.stopPropagation(); 
            $(this).parent().siblings().removeClass('open');
            $(this).parent().toggleClass('open');
        });
    });
})(jQuery);

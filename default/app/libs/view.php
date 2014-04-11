<?php
/**
 * @see KumbiaView
 */
require_once CORE_PATH . 'kumbia/kumbia_view.php';

/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 */
class View extends KumbiaView {
    
    /**
     * Método que muestra el contenido de una vista
     */
    public static function content() {                
        Flash::output();        
        parent::content();
    }
    
    /**
     * Método para mostrar los mensajes e impresiones del request
     */
    public static function flash() {        
        return self::partial('flash');        
    }
    
    /**
     * Método para mostrar el proceso actual en las vistar
     */
    public static function process($moduleName, $processName=null, $setTitle=true) {
        return self::partial('process', false, array('modulo'=>$moduleName, 'proceso'=>$processName, 'titulo'=>$setTitle));
    }
    
    /**
     * Método para dar una respuesta como ajax
     */
    public static function ajax() {
        View::select(NULL, NULL);
        return self::partial('ajax');
    }
    
    /**
     * Método que muestra una vista para redireccionar si se trabaja con ajax la app
     * 
     * @param string $url Url a redireccionar
     * @param boolean $isLogin Indica si redirecciona al login o no
     * @param string $text Texto a mostrar en la redirección
     * @return string
     */
    public static function redirect($url, $isLogin=FALSE, $text='') {
        View::select(NULL, NULL);
        return self::partial('redirect', FALSE, array('url'=>$url, 'isLogin'=>$isLogin, 'text'=>$text));
    }
    
    /**
     * Método para enviar un json
     */
    public function json() {
        View::select(NULL, 'json');
    }
    
    /**
     * Método para mostrar el mensaje de actualizacion
     */
    public static function appUpdate() {
        View::select(NULL, NULL);
        return self::partial('dbkm_update');
    }
    
    /**
     * Método para mostrar una ventana de error
     */
    public static function error($template='backend/error') {
        self::$_path = '_shared/errors/';
        self::select('popup', $template);
    }
    
    /**
     * Método para saber si existe una vista de eror      
     */
    public static function hasError($template='backend/error') {
        return (self::$_template == $template) ? true : false;            
    }
    
    /**
     * Método que muestra el reporte según el formato. Si es un formato desconocido muesra la página de error
     *
     * @param string $formato Formato a mostrar: html, pdf, xls, xml, ticket, etc
     * @return boolean
     */
    public static function report($formato) {     
        
        $formato    = Filter::get($formato,'string');
        $tipos      = explode('|', TYPE_REPORTS);
        $templates  = array();
        foreach($tipos as $tmp) {
            $r = explode('.', $tmp);
            if(count($r) > 1) {
                $templates[$r[0]] = $r[1];
            }
        }

        if(array_key_exists($formato, $templates)) {
            $template       = 'backend/'.$templates[$formato];
            $tmp_formato    = $formato.'.'.$templates[$formato];
        } else {
            $template = NULL;
            $tmp_formato    = $formato;
        }

        if($formato == 'error') {
            self::error();
        } else if( !in_array($tmp_formato, $tipos) OR $formato == null) {
            Flash::error('Error: El formato del reporte es incorrecto.');                
            self::error();
        } else {                   
            self::response($formato, $template);
        }        
        
    }
    

}

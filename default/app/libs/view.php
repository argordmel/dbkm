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
    

}

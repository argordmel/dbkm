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

}

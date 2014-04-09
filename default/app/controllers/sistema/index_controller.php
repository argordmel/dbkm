<?php
/**
 *
 * Descripcion: Controlador index para el manejo del sistema
 *
 * @category    
 * @package     Controllers  
 */

class IndexController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        return Redirect::to('dashboard');
    }
    
    
    /**
     * Método principal
     */
    public function index() {
        
    }
    
}


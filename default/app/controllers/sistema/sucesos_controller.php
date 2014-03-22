<?php
/**
 *
 * Descripcion: Controlador que se encarga de la visualización de los logs del sistema
 *
 * @category    
 * @package     Controllers  
 */

Load::models('sistema/sistema');

class SucesosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Visor de sucesos';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Logs del sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        if(Input::hasPost('corte')) {              
            return Redirect::toAction('listar/'.Input::post('corte'));
        }        
    }
    
    /**
     * Método para listar las autitorías del sistema
     * @param type $fecha
     * @return type
     */
    public function listar($fecha='', $page=1) {
        
        $fecha = empty($fecha) ? date("Y-m-d") : Filter::get($fecha, 'date');
        if(empty($fecha)) {
            Flash::info('Selecciona la fecha del archivo');
            return Redirect::toAction('index');
        }
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
              
        $loggers = Sistema::getLogger($fecha, $page);
        $this->loggers = $loggers;
        $this->fecha = $fecha;
        $this->page_module = 'Logs del sistema '.$fecha;
        
    }
        
}


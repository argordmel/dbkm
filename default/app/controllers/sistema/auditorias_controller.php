<?php
/**
 * 
 * Descripcion: Controlador que se encarga de la visualización de los logs del sistema
 *
 * @category    
 * @package     Controllers 
 */

Load::models('sistema/sistema');

class AuditoriasController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Auditoría y seguimientos';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Listado de acciones de los usuarios';
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
    public function listar($fecha='', $page='page.1') {
        $fecha              = empty($fecha) ? date("Y-m-d") : Filter::get($fecha, 'date');
        if(empty($fecha)) {
            Flash::info('Selecciona la fecha del archivo');
            return Redirect::toAction('index');
        }
        $page               = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;              
        $audits             = Sistema::getAudit($fecha, $page);
        $this->audits       = $audits;
        $this->fecha        = $fecha;
        $this->page_module  = 'Auditorías del sistema '.$fecha;
        
    }
        
}


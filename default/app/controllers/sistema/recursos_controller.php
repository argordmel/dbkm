<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de los recursos del sistema
 *
 * @category    
 * @package     Controllers  
 */

class RecursosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Recursos del sistema';
    }
    
    /**
     * Método principal
     */
    public function index() {
        Redirect::toAction('listar');
    }
    
    /**
     * Método para listar
     */
    public function listar($order='order.controlador.asc', $page='page.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $recurso = new Recurso();
        $this->recursos = $recurso->getListadoRecursoPorModulo('todos', $order, $page);                
        $this->order = $order;        
        $this->page_title = 'Listado de recursos del sistema';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {
        if(Input::hasPost('recurso')) {
            if(Recurso::setRecurso('create', Input::post('recurso'), array('activo'=>Recurso::ACTIVO))){
                Flash::valid('El recurso se ha registrado correctamente!');
                return Redirect::toAction('listar');
            }          
        }
        $this->page_title = 'Agregar recurso';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = Security::getKey($key, 'upd_recurso', 'int')) {
            return Redirect::toAction('listar');
        }
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            Flash::error('Lo sentimos, no se ha podido establecer la información del recurso');    
            return Redirect::toAction('listar');
        }
        
        if(empty($recurso->custom) && Session::get('perfil_id') != Perfil::SUPER_USUARIO) {
            Flash::warning('Lo sentimos, pero este recurso no se puede editar.');
            return Redirect::toAction('listar');
        }
        
        if(Input::hasPost('recurso')) {            
            if(Recurso::setRecurso('update', Input::post('recurso'), array('id'=>$id))){
                Flash::valid('El recurso se ha actualizado correctamente!');
                return Redirect::toAction('listar');
            }
        }
            
        $this->recurso = $recurso;
        $this->page_title = 'Actualizar recurso';
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_recurso', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            Flash::error('Lo sentimos, no se ha podido establecer la información del recurso');    
        } else {
            if(empty($recurso->custom) && Session::get('perfil_id') != Perfil::SUPER_USUARIO) {
                Flash::warning('Lo sentimos, pero este recurso no se puede editar.');
                return Redirect::toAction('listar');
            }
            if($tipo=='inactivar' && $recurso->activo == Recurso::INACTIVO) {
                Flash::info('El recurso ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $recurso->activo == Recurso::ACTIVO) {
                Flash::info('El recurso ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Recurso::INACTIVO : Recurso::ACTIVO;
                if(Recurso::setRecurso('update', $recurso->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Recurso::ACTIVO) ? Flash::valid('El recurso se ha reactivado correctamente!') : Flash::valid('El recurso se ha inactivado correctamente!');
                }
            }                
        }
        
        return Redirect::toAction('listar');
    }
    
    /**
     * Método para eliminar
     */
    public function eliminar($key) {         
        if(!$id = Security::getKey($key, 'eliminar_recurso', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $recurso = new Recurso();
        if(!$recurso->find_first($id)) {
            Flash::error('Lo sentimos, no se ha podido establecer la información del recurso');    
            return Redirect::toAction('listar');
        }              
        try {
            if($recurso->delete()) {
                Flash::valid('El recurso se ha eliminado correctamente!');
            } else {
                Flash::warning('Lo sentimos, pero este recurso no se puede eliminar.');
            }
        } catch(KumbiaException $e) {
            Flash::error('Este recurso no se puede eliminar porque se encuentra relacionado con otro registro.');
        }
        
        return Redirect::toAction('listar');
    }
    
}


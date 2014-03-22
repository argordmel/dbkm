<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de los permisos a los perfiles de usuarios
 *
 * @category    
 * @package     Controllers  
 */

class PermisosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de permisos';
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
    public function listar($order='order.modulo.asc') { 
        
        if(Input::hasPost('privilegios') OR Input::hasPost('old_privilegios')) {
            if(RecursoPerfil::setRecursoPerfil(Input::post('privilegios'), Input::post('old_privilegios'))) {
                Flash::valid('Los privilegios se han registrado correctamente!');                
                Input::delete('privilegios');//Para que no queden persistentes
                Input::delete('old_privilegios');
            }
        }
        
        $recurso = new Recurso();
        $this->recursos = $recurso->getListadoRecursoPorModulo(Recurso::ACTIVO);
        
        $perfil = new Perfil();
        $this->perfiles = $perfil->getListadoPerfil(Perfil::ACTIVO);
        
        $privilegio = new RecursoPerfil();
        $this->privilegios = $privilegio->getPrivilegiosToArray();
        
        $this->order = $order;        
        $this->page_title = 'Permisos y privilegios de usuarios';        
    }   
}


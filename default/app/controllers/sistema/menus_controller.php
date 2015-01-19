<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de los menús del sistema
 *
 * @category
 * @package     Controllers
 */

class MenusController extends BackendController {

    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de menús';
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
    public function listar($order='order.posicion.asc', $page='page.1') {
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $menu = new Menu();
        $this->menus = $menu->getListadoEdicion(Menu::BACKEND);
        $this->front = $menu->getListadoEdicion(Menu::FRONTEND);
        $this->order = $order;
        $this->page_title = 'Listado de menús del sistema';
    }

    /**
     * Método para agregar
     */
    public function agregar() {
        if(Input::hasPost('menu')) {
            if(Menu::setMenu('create', Input::post('menu'), array('activo'=>Menu::ACTIVO))){
                if(APP_AJAX) {
                    Flash::valid('El menú se ha creado correctamente! <br/>Por favor recarga la página para verificar los cambios.');
                } else {
                    Flash::valid('El menú se ha creado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }
        $this->page_title = 'Agregar menú';
    }

    /**
     * Método para editar
     */
    public function editar($key) {
        if(!$id = Security::getKey($key, 'upd_menu', 'int')) {
            return Redirect::toAction('listar');
        }

        $menu = new Menu();
        if(!$menu->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido establecer la información del menú');
            return Redirect::toAction('listar');
        }

        if($menu->id <= 2) {
            Flash::warning('Lo sentimos, pero este menú no se puede editar.');
            return Redirect::toAction('listar');
        }

        if(Input::hasPost('menu')) {
            if(Menu::setMenu('update', Input::post('menu'), array('id'=>$id))){
                if(APP_AJAX) {
                    Flash::valid('El menú se ha actualizado correctamente! <br/>Por favor recarga la página para verificar los cambios.');
                } else {
                    Flash::valid('El menú se ha actualizado correctamente!');
                }
                return Redirect::toAction('listar');
            }
        }

        $this->menu = $menu;
        $this->page_title = 'Actualizar menú';

    }

    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = Security::getKey($key, $tipo.'_menu', 'int')) {
            return Redirect::toAction('listar');
        }

        $menu = new Menu();
        if(!$menu->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido establecer la información del menú');
        } else {
            if($menu->id <= 2) {
                Flash::warning('Lo sentimos, pero este menú no se puede editar.');
                return Redirect::toAction('listar');
            }
            if($tipo=='inactivar' && $menu->activo == Menu::INACTIVO) {
                Flash::info('El menú ya se encuentra inactivo');
            } else if($tipo=='reactivar' && $menu->activo == Menu::ACTIVO) {
                Flash::info('El menú ya se encuentra activo');
            } else {
                $estado = ($tipo=='inactivar') ? Menu::INACTIVO : Menu::ACTIVO;
                if(Menu::setMenu('update', $menu->to_array(), array('id'=>$id, 'activo'=>$estado))){
                    ($estado==Menu::ACTIVO) ? Flash::valid('El menú se ha reactivado correctamente!') : Flash::valid('El menú se ha inactivado correctamente!');
                }
            }
        }

        return Redirect::toAction('listar');
    }

    /**
     * Método para eliminar
     */
    public function eliminar($key) {
        if(!$id = Security::getKey($key, 'eliminar_menu', 'int')) {
            return Redirect::toAction('listar');
        }

        $menu = new Menu();
        if(!$menu->find_first($id)) {
            Flash::error('Lo sentimos, pero no se ha podido establecer la información del menú');
            return Redirect::toAction('listar');
        }
        if($menu->id <= 2) {
            Flash::warning('Lo sentimos, pero este menú no se puede eliminar.');
            return Redirect::toAction('listar');
        }
        try {
            if($menu->delete()) {
                Flash::valid('El menú se ha eliminado correctamente!');
            } else {
                Flash::warning('Lo sentimos, pero este menú no se puede eliminar.');
            }
        } catch(KumbiaException $e) {
            Flash::error('Este menú no se puede eliminar porque se encuentra relacionado con otro registro.');
        }

        return Redirect::toAction('listar');
    }

}


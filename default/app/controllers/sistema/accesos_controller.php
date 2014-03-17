<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Descripcion: Controlador que se encarga de la gestión de los accesos de los usuarios
 *
 * @category    
 * @package     Controllers 
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co)
 */

class AccesosController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Accesos al sistema';
    }
    
    
    /**
     * Método principal
     */
    public function index() {
        Redirect::toAction('listar');
    }
    
    
    /**
     * Método para listar
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function listar($order='order.fecha.desc', $page='page.1') { 
        $page               = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $acceso             = new Acceso();
        $this->accesos      = $acceso->getListadoAcceso(NULL, 'todos', $order, $page);        
        $this->order        = $order;        
        $this->page_title   = 'Entrada y salida de usuarios';
    }        
    
    
    /**
     * Método para buscar
     * 
     * @param type $field Nombre del campo a buscar
     * @param type $value Valor del campo
     * @param type $order Método de ordenamiento
     * @param type $page Número de página
     */
    public function buscar($field='nombre', $value='none', $order='order.fecha.asc', $page='page.1') {        
        $page       = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field      = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value      = (Input::hasPost('value')) ? Input::post('value') : $value;
        $acceso     = new Acceso();
        $accesos    = $acceso->getAjaxAcceso($field, $value, $order, $page);
        if(empty($accesos->items)) {
            Flash::info('No se han encontrado registros');
        }
        $this->accesos      = $accesos;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de ingresos al sistema';        
    }
    
}


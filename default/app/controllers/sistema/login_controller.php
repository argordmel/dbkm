<?php
/**
 *
 * Descripcion: Controlador que se encarga del logueo de los usuarios del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      argordmel 
 */

Load::lib('security');

class LoginController extends BackendController {
    
    /**
     * Limite de parámetros por acción
     */
    public $limit_params = FALSE;
    
    /**
     * Nombre de la página
     */
    public $page_title = 'Entrar';
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        View::template('backend/login');
    }
    
    /**
     * Método principal     
     */
    public function index() {        
        return Redirect::toAction('entrar/');
    }
    
    /**
     * Método para iniciar sesión
     */
    public function entrar() {         
        if(Input::hasPost('login') && Input::hasPost('password') && Input::hasPost('mode')) {
            if(Usuario::setSession('open', Input::post('login'), Input::post('password'))) {
                return Redirect::to('dashboard/');
            } else {
                //Se soluciona lo de la llave de seguridad
                return Redirect::toAction('entrar/');
            }                      
        } else if(DwAuth::isLogged()) {
            return Redirect::to('dashboard/');
        }
    }
    
    /**
     * Método para cerrar sesión
     */
    public function salir($js='') {        
        if(Usuario::setSession('close')) {
            Flash::valid("La sesión ha sido cerrada correctamente.");
        }
        if($js == 'no-script') {
            Flash::info('Activa el uso de JavaScript en su navegador para poder continuar.');
        }        
        return Redirect::toAction('entrar/');
    }
    
}


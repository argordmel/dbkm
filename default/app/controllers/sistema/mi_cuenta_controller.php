<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de las cuentas de usuario
 *
 * @category
 * @package     Controllers 
 */

class MiCuentaController extends BackendController {

    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Mi Cuenta';
    }

    /**
     * Método principal
     */
    public function index() {
        $usuario = new Usuario();
        if(!$usuario->getInformacionUsuario(Session::get('id'))) {
            Flash::info('Lo sentimos pero no se ha podido establecer tu información');
            return Redirect::to('dashboard');
        }

        $perfil         = $usuario->perfil;
        $app_ajax_old   = $usuario->app_ajax;
        $tmp_usr        = $usuario;
        
        if(Input::hasPost('usuario')) {            
            $usuario = Usuario::setUsuario('update', Input::post('usuario'), array('repassword'=>Input::post('repassword'), 'oldpassword'=>Input::post('oldpassword'), 'id'=>$usuario->id, 'login'=>$usuario->login, 'perfil_id'=>$usuario->perfil_id));
            if($usuario) {                
                Flash::valid('El usuario se ha actualizado correctamente.');
                if($app_ajax_old != $usuario->app_ajax) {
                    Session::set('app_ajax', $usuario->app_ajax);
                    return (APP_AJAX) ? View::redirect(PUBLIC_PATH.'sistema/mi_cuenta/') : Redirect::to('/sistema/mi_cuenta');
                }
                $usuario->perfil = $perfil;                
            } else {
                $usuario = $tmp_usr;
            }
        }
        
        $this->temas = DwUtils::getFolders(dirname(APP_PATH).'/public/css/backend/themes/');
        $this->usuario = $usuario;
        $this->page_title = 'Actualizar mis datos';
    }

    /**
     * Método para subir imágenes
     */
    public function upload() {
        $upload = new DwUpload('fotografia', 'img/upload/personas/');
        $upload->setAllowedTypes('png|jpg|gif|jpeg');
        $upload->setEncryptName(TRUE);
        $upload->setSize('3MB', 170, 200, TRUE);        
        if(!$data = $upload->save()) { //retorna un array('path'=>'ruta', 'name'=>'nombre.ext');
            $data = array('error'=>true, 'message'=>$upload->getError());
        }
        sleep(1);//Por la velocidad del script no permite que se actualize el archivo
        $this->data = $data;
        View::json();
    }

}


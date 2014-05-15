<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */

//Cargo los modelos básicos
Load::models('sistema/usuario', 'sistema/menu');

class BackendController extends Controller {
    
    /**
    * Titulo de la página
    */
    public $page_title = 'Página sin título';

    /**
    * Nombre del módulo en el que se encuentra
    */
    public $page_module = 'Indefinido';

    /**
    * Tipo de formato del reporte
    */
    public $page_format;

    /**
     * Variable que indica el cambio de título de la página en las respuestas ajax
     */
    public $set_title = TRUE;
    
    /**
     * Inicio de transacciones
     */
    protected $_time_request;


    final protected function initialize() {
        
        /**
         * Si el método de entrada es ajax, el tipo de respuesta es sólo la vista
         */
        if(Input::isAjax()) {
            View::template(null);
            if(!empty($_POST)) {
                Session::set('change_url', TRUE);
            }            
        }
        
        /**
         * Verifico que haya iniciado sesión
         */
        if( !DwAuth::isLogged() ) {
            //Verifico que no genere una redirección infinita
            if( ($this->controller_name != 'login') && ( $this->action_name != 'entrar' && $this->action_name != 'salir') ) {
                Flash::warning('No has iniciado sesión o ha caducado.');
                //Verifico que no sea una ventana emergente
                if($this->module_name == 'reporte') {
                    View::error();
                } else {
                    (Input::isAjax()) ? View::redirect('sistema/login/entrar/', TRUE) : Redirect::to('sistema/login/entrar/');                    
                }
                return false;
            }
        } else if( DwAuth::isLogged() && $this->controller_name!='login' ) {
            
            if(!defined('SKIN')) {
                define('SKIN', Session::get('tema'));
            }
            
            $acl = new DwAcl(); //Cargo los permisos y templates
            if (!$acl->check(Session::get('perfil_id'))) {
                Flash::error('Tu no posees privilegios para acceder a <b>' . Router::get('route') . '</b>');
                if(Input::isAjax()) {
                    View::ajax();
                    header('http/1.1 403 forbidden'); //Agrego la cabecera de forbidden
                } else {
                    View::select(NULL);
                }               
                return FALSE;
            }
            
            if(APP_UPDATE && (Session::get('perfil_id') != Perfil::SUPER_USUARIO) ) { //Solo el super usuario puede hacer todo
                if($this->module_name!='dashboard' && $this->controller_name!='index') {
                    $msj = 'Estamos en labores de actualización y mantenimiento.';
                    $msj.= '<br />';
                    $msj.= 'El servicio se reanudará dentro de '.APP_UPDATE_TIME;
                    if(Input::isAjax()) {
                        View::appUpdate();
                    } else {
                        if($this->module_name != 'dashboard' OR ($this->module_name == 'dashboard' && $this->controller_name != 'index') ) {
                            Flash::info($msj);
                            Redirect::to('dashboard');
                        }
                    }
                    return FALSE;
                }
            }
        }

    }

    final protected function finalize() {
        $this->page_title = trim($this->page_title).' | '.APP_NAME;
        
        //Se muestra la vista según el tipo de reporte
        if(Router::get('module') == 'reporte') {
            View::report($this->page_format);
        }
        
        //Se verifica si se cambia el título de la página, cuando se hacen peticiones por ajax
        if($this->set_title && Input::isAjax()) {
            $this->set_title = TRUE;
        }
    }

}

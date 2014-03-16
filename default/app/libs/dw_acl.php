<?php
/**
 *
 * Clase que se utiliza para validar los accesos a los usuarios
 *
 * @category    Sistema
 * @package     Libs
 * @version     1.0
 */

Load::lib('acl2');

Load::models('sistema/perfil');

class DwAcl {
    
    /**
     *
     * @var SimpleAcl
     */
    static protected $_acl = null;
           
    /**
     * arreglo con los templates para cada usuario
     *
     * @var array 
     */
    protected $_templates = array();
    
    /**
     * Método constructor
     */
    public function __construct() {
        self::$_acl = Acl2::factory('simple');  
        $perfil     = new Perfil();
        $perfiles   = $perfil->getListadoPerfil('acl');
        $this->_setPerfiles($perfiles);        
    }
    
    /**
     * Método para establecer los perfiles del sistema
     * @param type $perfiles
     */
    protected function _setPerfiles($perfiles) {
        foreach ($perfiles as $perfil) {
            if ($perfil->estado==Perfil::ACTIVO) {                 
                self::$_acl->user($perfil->id, array($perfil->id));                
                $plantilla = empty($perfil->plantilla) ? 'default' : $perfil->plantilla;                
                $this->_setTemplate($perfil->id, $plantilla);
                $this->_setRecursos($perfil->id, $perfil->getRecursos($perfil->id));
            }
        }
    }
   
    /**
     * Método para cargar los templates según los perfiles
     * @param type $perfil
     * @param type $template
     */
    protected function _setTemplate($perfil, $template) {        
        $this->_templates["$perfil"] = $template;        
    }
        
    /**
     * Método que carga los recursos segun los perfiles definidos
     * @param type $perfil
     * @param type $recursos
     */
    protected function _setRecursos($perfil, $recursos) {
        $urls = array();
        foreach ($recursos as $recurso) {  
            if($recurso->activo == Recurso::ACTIVO) {                     
                $urls[] = $recurso->recurso;            
            }
        }        
        self::$_acl->allow($perfil, $urls);
    }
    
    /**
     * Método para verificar si tiene acceso al recurso
     * @return boolean
     */
    public function check($perfil) {        
        $modulo         = Router::get('module');
        $controlador    = Router::get('controller');
        $accion         = Router::get('action'); 
        if (isset($this->_templates["$perfil"]) && !Input::isAjax()) {
            View::template("backend/{$this->_templates["$perfil"]}");
        }
        if ($modulo) {
            $recurso1 = "$modulo/$controlador/$accion";//Por si tiene acceso a una única acción
            $recurso2 = "$modulo/$controlador/*";  //por si tiene acceso a todas las acciones
            $recurso3 = "$modulo/*/*";  //por si tiene acceso a todos los controladores
            $recurso4 = "*";  //por si tiene acceso a todo el sistema
        } else {
            $recurso1 = "$controlador/$accion";//Por si tiene acceso a una única acción
            $recurso2 = "$controlador/*"; //por si tiene acceso a todas las acciones
            $recurso3 = "$modulo/*/*";  //por si tiene acceso a todos los controladores
            $recurso4 = "*";  //por si tiene acceso a todo el sistema
        }        
        //Flash::info("Perfil: $perfil <br /> Recurso 1: $recurso1 <br /> Recurso 2: $recurso2 <br /> Recurso 3: $recurso3 <br /> Recurso 4: $recurso4");
        return self::$_acl->check($recurso1, $perfil) ||
                self::$_acl->check($recurso2, $perfil) ||
                self::$_acl->check($recurso3, $perfil) ||
                self::$_acl->check($recurso4, $perfil);
    }        
    
}

?>

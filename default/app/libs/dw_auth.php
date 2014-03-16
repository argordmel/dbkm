<?php
/**
 *
 * Clase que se utiliza para autenticar los usuarios
 *
 * @category    Sistema
 * @package     Libs 
 */

Load::lib('auth2');

//Cambiar la key por una propia
//Se puede utilizar las de wordpress: https://api.wordpress.org/secret-key/1.1/salt/
define('SESSION_KEY', 'MXKocr!!GVeO{ocb$%[7QK=8{]oozs]5D;ngQ^yz+0-O9H#>d9BD!&u.<Yg3$u~=');

class DwAuth {
    
    /**
     * Mensaje de Error
     *
     * @var String
     */
    protected static $_error = null;
    
    /**
    * Método para iniciar Sesion
    *
    * @param $username mixed Array con el nombre del campo en la bd del usuario y el valor
    * @param $password mixed Array con el nombre del campo en la bd de la contraseña y el valor
    * @return true/false
    */
    public static function login($fieldUser, $fieldPass) {        
        //Verifico si tiene una sesión válida
        if(self::isLogged()) {
            return true;
        } else {
                        
            //Verifico si envía el array array('usuario'=>'admin') o string 'usuario'
            $keyUser = (is_array($fieldUser)) ? @array_shift(array_keys($fieldUser)) : NULL;
            $keyPass = (is_array($fieldPass)) ? @array_shift(array_keys($fieldPass)) : NULL;
            $valUser = ($keyUser) ? $fieldUser[$keyUser] : NULL;
            $valPass = ($keyPass) ? $fieldPass[$keyPass] : NULL;
            
            if(empty($valUser) OR empty($valPass)) {
                self::setError("Ingresa el usuario y contraseña");
                return false;
            }            
            
            $auth = Auth2::factory('model');            
            ($keyUser) ? $auth->setLogin($keyUser) : $auth->setLogin($fieldUser);
            ($keyPass) ? $auth->setPass($keyPass) : $auth->setPass($fieldPass);
            $auth->setAlgos('sha1');
            $auth->setCheckSession(true);
            $auth->setModel('sistema/usuario');                                        
            $auth->setFields(array('id', 'nombre', 'apellido', 'login', 'tema', 'app_ajax', 'datagrid', 'perfil_id', 'pool', 'fotografia'));                                                
            if($auth->identify($valUser, $valPass) && $auth->isValid()) {  
                Session::set(SESSION_KEY, true);
                return true;
            } else {                
                self::setError('El usuario y/o la contraseña son incorrectos.');                
                Session::set(SESSION_KEY, false);                
                return false;
            }
        }
    }
    
    /**
    * Método para cerrar sesión
    *
    * @param void
    * @return void
    */
    public static function logout() {
        //Verifico si tiene sesión
        if(!self::isLogged()) { 
            self::setError("No has iniciado sesión o ha caducado. <br /> Por favor identifícate nuevamente.");
            return false;
        } else {                  
            $auth = Auth2::factory('model');
            $auth->logout();            
            Session::set(SESSION_KEY, false);
            unset($_SESSION['KUMBIA_SESSION'][APP_PATH]);
            return true;
        }
    }
        
    /**
    * Método para verificar si tiene una sesión válida
    *
    * @param void
    * @return ture/false
    */
    public static function isLogged() {
        
        $usuario = new Usuario();        
        $auth = Auth2::factory('model');
        $bValid = $auth->isValid();        
        $bValid = $bValid && Session::get(SESSION_KEY);        
        return $bValid;         
    }
    
    /**
    * @return string
    */
    public static function getError() {
        return self::$_error;
    }
    
    /**
    * @param string $_error
    */
    public static function setError($error) {
        self::$_error = $error;
    }
}

?>

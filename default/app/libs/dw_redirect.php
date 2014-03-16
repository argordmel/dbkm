<?php
/**
 *
 * Descripcion: Clase para el manejo de redirecciones internas
 *
 * @category    
 * @package     Libs 
 */

class DwRedirect {
     
    /**
     * Redirecciona la ejecución a otro controlador en un
     * tiempo de ejecución determinado
     *
     * @param string $route ruta a la que será redirigida la petición.
     * @param integer $seconds segundos que se esperarán antes de redirigir
     * @param integer $statusCode código http de la respuesta, por defecto 302
     */
    public static function to($route = null, $seconds = null, $statusCode = 302) {          
        $route = trim($route, '/').'/';
        if(APP_AJAX && Input::isAjax()) { //Si se redirecciona estando la app en ajax                        
            $route = PUBLIC_PATH.$route;
            View::redirect($route);            
        } else {
            Redirect::to($route, $seconds, $statusCode);
        }        
    }
    
    /**
     * Redirecciona a un método del mismo controlador
     * 
     * @param string $action Nombre del método dentro del controlador
     * @param string $params Parámetros a pasar por la url
     * @example DwRouter::toAction('listar', 'pag/2');
     */
    public static function toAction($action, $params=null) { 
        $action = trim($action, '/');
        $params = trim($params, '/');
        if(Input::isAjax() && APP_AJAX) {
            $url = empty($params) ? Router::get('controller_path')."/$action/" : Router::get('controller_path')."/$action/$params/";
            echo DwJs::updateUrl($url);//Aplico el hash a la url para saber la ruta actual
            empty($params) ? Redirect::route_to("action: $action") : Redirect::route_to("action: $action", "parameters: $params");
        } else {            
            empty($params) ? Redirect::toAction("$action/") : Redirect::toAction("$action/$params/");
        }
    } 
    
    /**
     * Enruta a un modelo, controlador, accion y pasa parámetros      
     */
    public static function toRoute(){
        $url = Util::getParams(func_get_args());
        if(!isset($url['module'])) {
            $url['module'] = null;
        }
        if(!isset($url['action'])) {
            $url['action'] = 'index';
        }
        if(!isset($url['parameters'])) {
            $url['parameters'] = null;
        }
        if(Input::isAjax() && APP_AJAX) {
            $href = trim("{$url['module']}/{$url['controller']}/{$url['action']}/{$url['parameters']}/", '/');
            echo DwJs::updateUrl($href);
        }        
        if($url['parameters']==null) {
            Redirect::route_to("module: {$url['module']}", "controller: {$url['controller']}", "action: {$url['action']}");
        } else {
            Redirect::route_to("module: {$url['module']}", "controller: {$url['controller']}", "action: {$url['action']}", "parameters: {$url['parameters']}");
        }                        
    }
    
    /**
     * Redirecciona a la página de inicio de sesión     
     */
    public static function toLogin($path='sistema/login/entrar/') {
        $path = trim($path, '/').'/';        
        if(Input::isAjax()) { //Si se redireciona mediante una entrada de ajax
            View::redirectToLogin($path);
        } else {            
            Redirect::to($path);
        }
    } 
}


<?php
/**
 *
 * Extension para renderizar los menús
 *
 * @category    Helpers
 * @package     Helpers
 */

Load::models('sistema/menu');

class DwMenu {
    
    /**
     * Variable que contiene los menús 
     */
    protected static $_main = null;
    
    /**
     * Variable que contien los items del menú
     */        
    protected static $_items = null;
    
    /**
     * Variabla para indicar el entorno
     */
    protected static $_entorno;
    
    /**
     * Variable para indicar el perfil
     */
    protected static $_perfil;
    
    
    /**
     * Método para cargar en variables los menús
     * @param type $perfil
     */
    public static function load($entorno, $perfil=NULL) {        
        self::$_entorno = $entorno; 
        self::$_perfil = $perfil;
        $menu = new Menu();
        if(self::$_main==NULL) {                        
            self::$_main = $menu->getListadoMenuPadres($entorno, $perfil);
        }        
        if(self::$_items==NULL && self::$_main) {
            foreach(self::$_main as $menu) {                            
                self::$_items[$menu->menu] = $menu->getListadoSubmenu($entorno, $menu->id, $perfil);
            }
        }
        
    }
       
    /**
     * Método para renderizar el menú de escritorio
     */
    public static function desktop() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="nav navbar-nav">'.PHP_EOL;
            foreach(self::$_main as $main) {         
                $active = ($main->url==$route) ? 'active' : null;
                if(self::$_entorno==Menu::BACKEND) {
                    $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, array('class'=>'main-menu-link', 'data-filter'=>"sub-menu-".DwUtils::getSlug($main->menu)), $main->icono).'</li>'.PHP_EOL;
                } else {
                    if(!array_key_exists($main->menu, self::$_items)) {
                        $text = $main->menu.'<b class="caret"></b>';
                        $html.= '<li class="dropdown">';                        
                        $html.= DwHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), NULL, FALSE);
                        $html.= '<ul class="dropdown-menu">';
                        foreach(self::$_items[$main->menu] as $item) {                        
                            $active = ($item->url==$route) ? 'active' : null;
                            $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, APP_AJAX).'</li>';
                        }                        
                        $html.= '</ul>';
                        $html.= '</li>';
                    } else {
                        $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, NULL, $main->icono, APP_AJAX).'</li>'.PHP_EOL;
                    }
                }
            }
            $html.= '</ul>'.PHP_EOL;
        }        
        return $html;
    }
    
    /**
     * Método para renderizar el menú de dispositivos móviles     
     */
    public static function phone() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="nav navbar-nav">';
            foreach(self::$_main as $main) {
                $text = $main->menu.'<b class="caret"></b>';
                $html.= '<li class="dropdown">';
                $html.= DwHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), $main->icono, TRUE);
                if(array_key_exists($main->menu, self::$_items)) {
                    $html.= '<ul class="dropdown-menu">';
                    foreach(self::$_items[$main->menu] as $item) {                         
                        $active     = ($item->url==$route) ? 'active' : null;   
                        $submenu    = $item->getListadoSubmenu(self::$_entorno, $item->id, self::$_perfil);
                        if($submenu) {                            
                            $html.= '<li class="'.$active.' dropdown dropdown-submenu">';
                            $html.= DwHtml::link($item->url, $item->menu.' <b class="caret"></b>', array('class'=>'dropdown-toggle', 'role'=>"button", "data-toggle"=>"dropdown"), NULL);
                            $html.= '<ul class="dropdown-menu">';
                            foreach($submenu as $tmp) {
                                $html.= '<li>'.DwHtml::link($tmp->url, $tmp->menu, null, $tmp->icono).'</li>'.PHP_EOL;
                            }
                            $html.= '</ul>';
                            $html.= '</li>';
                        } else {                            
                            $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, TRUE).'</li>';
                        }                        
                    }
                    $html.= '</ul>';
                }
                $html.= '</li>'.PHP_EOL;
                //$html.= '<li class="divider"></li>'.PHP_EOL;
            }
            $html.= '</ul>';

        }
        return $html;
    }
    
    /**
     * Método para listar los items en el backend
     */
    public static function getItems() {
        $route = trim(Router::get('route'), '/');
        $html = '';        
        foreach(self::$_items as $menu => $items) {
            $html.= '<div id="sub-menu-'.DwUtils::getSlug($menu).'" class="subnav hidden">'.PHP_EOL;
            $html.= '<ul class="nav nav-pills">'.PHP_EOL;
            if(array_key_exists($menu, self::$_items)) {
                foreach(self::$_items[$menu] as $item) {                    
                    $active = ($item->url==$route or $item->url=='principal') ? 'active' : null;
                    $submenu = $item->getListadoSubmenu(self::$_entorno, $item->id, self::$_perfil);
                    if($submenu) {
                        $html.= '<li class="'.$active.'dropdown">';
                        $html.= DwHtml::link($item->url, $item->menu.' <b class="caret"></b>', array('class'=>'dropdown-toggle', 'role'=>"button", "data-toggle"=>"dropdown"), $item->icono);                        
                        $html.= '<ul class="dropdown-menu" role="menu">';
                        foreach($submenu as $tmp) {
                            $html.= '<li>'.DwHtml::link($tmp->url, $tmp->menu, null, $tmp->icono).'</li>'.PHP_EOL;
                        }
                        $html.= '</ul>';
                        $html.= '</li>';
                    } else {
                        $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, null, $item->icono).'</li>'.PHP_EOL;                        
                    }
                }
            }
            $html.= '</ul>'.PHP_EOL;
            $html.= '</div>'.PHP_EOL;
        }
        return $html;  
    }
    
    
    /**
     * Método para renderizar el menú para el frontend
     */
    public static function frontend() {
        $route = trim(Router::get('route'), '/');
        $html = '';
        if(self::$_main) {
            $html.= '<ul class="nav navbar-nav">'.PHP_EOL;
            foreach(self::$_main as $main) {         
                $active = ($main->url==$route) ? 'active' : null;                
                if(empty(self::$_items[$main->menu])) {
                    $html.= '<li class="'.$active.'">'.DwHtml::link($main->url, $main->menu, NULL, $main->icono, FALSE).'</li>'.PHP_EOL;                   
                } else {
                    $text = $main->menu.'<b class="caret"></b>';
                    $html.= '<li class="dropdown">'; 
                    $html.= DwHtml::link('#', $text, array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'), NULL, FALSE);                        
                    $html.= '<ul class="dropdown-menu">';
                    foreach(self::$_items[$main->menu] as $item) {
                        $active = ($item->url==$route) ? 'active' : null;
                        $html.= '<li class="'.$active.'">'.DwHtml::link($item->url, $item->menu, NULL, $item->icon, FALSE).'</li>';                        
                    }
                    $html.= '</ul>';
                    $html.= '</li>';
                }
                
            }
            $html.= '</ul>'.PHP_EOL;
        }        
        return $html;
    }
    
}

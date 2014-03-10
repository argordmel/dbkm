<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Filtra numeros enteros entre letras
 *
 * @category    Extensions
 * @author      Iván D. Meléndez (ivan.melendez@dailycript.com.co)
 * @package     Filters
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class PageFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string
     *
     * @param string $s
     * @param array $options
     * @return string
     */    
    public static function execute ($s, $options) {
        $patron = '/[^0-9]/';
        return preg_replace($patron, '', (string) $s);        
    }    

}
?>

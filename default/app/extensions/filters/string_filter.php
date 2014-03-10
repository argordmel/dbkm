<?php
/**
 * Dailyscript - app | web | media
 *
 *
  *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class StringFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string en minúsculas
     *
     * @param string $s
     * @param array $options
     * @return string
     */
    public static function execute($s, $options) {        
        $string = filter_var($s, FILTER_SANITIZE_STRING);
        $string = strip_tags((string) $string);
        $string = stripslashes((string) $string);
        $string = trim($string);
        return $string;
   }

}
?>

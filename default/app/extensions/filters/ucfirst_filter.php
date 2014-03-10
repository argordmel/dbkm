<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2011 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class UcfirstFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para convertir a minúsculas incluyendo la Ñ y las tildes
     *
     * @param string $s
     * @param array $options
     * @return string
     */

    public static function execute($s, $options) {
        $string = mb_strtoupper(mb_substr($s, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($s, 1), 'UTF-8');
        return $string;
   }

}
?>

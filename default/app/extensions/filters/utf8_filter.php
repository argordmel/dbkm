<?php

/**
 * Dailyscript - Web | App | Media
 *
 * Filtro para usar tildes en los reportes
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2011 Dailyscript Team (http://www.dailyscript.co)
 * @version     1.0
 */
class Utf8Filter implements FilterInterface {

    /**
     * Ejecuta el filtro
     *
     * @param string $s
     * @param array $options
     * @return string
     */
    public static function execute($s, $options) {
        //Aplicar el filtro para decodificar los caracteres especiales de la base de datos
        $string = html_entity_decode(utf8_decode($s));
        return $string;
    }

}
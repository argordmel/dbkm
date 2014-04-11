<?php
/**
 *
 * Filtro para usar tildes en los reportes de pdf
 *
 * @category    Extensions
 * @package     Filters
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
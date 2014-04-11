<?php
/**
 * 
 * Filtra el texto a mayúsculas
 *
 * @category    Extensions
 * @package     Filters
 */

class UpperFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para convertir a minúsculas incluyendo la Ñ y las tildes
     *
     * @param string $s
     * @param array $options
     * @return string
     */

    public static function execute($s, $options) {
        $string     =   mb_strtoupper($s, 'UTF-8');
        return trim(ucfirst($string));
   }

}
?>

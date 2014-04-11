<?php
/**
 * Filtro para texto en minúscula
 *
 * @category    Extensions
 * @package     Filters
 */

class LowerFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string en minúsculas
     *
     * @param string $s
     * @param array $options
     * @return string
     */
    public static function execute($s, $options) {
        return mb_strtolower($s, 'UTF-8');
   }

}
?>

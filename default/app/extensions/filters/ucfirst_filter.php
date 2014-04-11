<?php
/**
 * Filtro para poner la primera letra en mayúscula
 *
 * @category    Extensions
 * @package     Filters
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

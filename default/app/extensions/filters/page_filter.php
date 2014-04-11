<?php
/**
 * Filtra numeros enteros entre letras
 *
 * @category    Extensions
 * @package     Filters
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

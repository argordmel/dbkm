<?php
/**
 *
 * @category    Librería para el manejo de archivos
 * @package     Libs
 */

class DwFile {
        
    /**
     * Path donde se encuentra el archivo
     *
     * @var string
     */
    protected static $_file_path = '';

    /**
     * Especifica el PATH donde se encuentra el archivo
     *
     * @param string $path
     */
    public static function set_path($path) {
        self::$_file_path = $path;
    }

    /**
     * Obtener el path actual
     *
     * @return $path
     */
    public static function get_path() {
        return self::$_file_path;
    }
    
    /**
     * Método que para leer un archivo plano
     * @return array
     */
    public static function read($name, $ext='txt') {        
        if(empty(self::$_file_path)) {
            self::$_file_path = APP_PATH . 'temp/logs/';
        }      
        $file = rtrim(self::$_file_path, '/') . '/' . $name . '.'.  $ext;
        if(is_file($file)) {            
            return file($file);
        } else {            
            return false;
        }        
    }    
    
}
?>

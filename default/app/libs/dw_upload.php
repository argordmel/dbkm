<?php
/**
 * Clase que se utiliza para la carga de archivos e imágenes
 *
 * @category    
 * @package     Libs 
 */

Load::lib('wideimage/WideImage');

class DwUpload {
    
    /**
     * Nombre del input del archivo
     *
     * @var string
     */
    public $file;
    
    /**
     * Extensión del achivo
     *
     * @var string
     */
    public $file_ext;
    
    /**
     * Nombre del archivo cargado
     *
     * @var string
     */
    public $oldName;
    
    /**
     * Nombre con el que se guardará el archivo
     *
     * @var string
     */
    public $name;
    /**
     * Ruta estática de las imágenes
     *
     * @var string
     */
    public $path;
    
    /**
     * Tipos de archivo definidos
     * 
     * @var string
     */    
    public $allowedTypes = '*';
    
    /**
     * Indica si cambia el nombre por un md5
     */
    public $encryptName = FALSE;
    
    /**
     * Permitir cambiar el tamaño de la imagen
     *
     * @var boolean
     */
    public $resize = FALSE;
    
    /**
     * Indica si al reescalar se mantiene la imagen anterior o no
     */
    public $removeOld = FALSE;
    
    /**
     * Ancho de la imagen reescalada
     *
     * @var boolean
     */
    public $resizeWidth = 170;

    /**
     * Alto de la imagen reescalada
     *
     * @var boolean
     */
    public $resizeHeight = 200;
    
    /**
     * Tamaño del archivo cargado
     * @var string
     */
    protected $size = 0;
    

    /**
     * Tamaño maximo del archivo
     *
     * @var string
     */
    public $maxSize = "2MB";
    
    /**
     * Variable con los mensajes de error
     *
     * @var string
     */
    protected $_error;
    
    /**
     * Constructor de la clase
     * @param string $input Nombre del input enviado
     * @param string $path Ruta donde se alojará el archivo
     */
    public function  __construct($input='', $path='') {  
        $this->file = $input;
        $this->path = dirname(APP_PATH) . '/public/'.trim($path, '/');        
    }        
    
    /**
     * Función que se encarga de gestionar el archivo
     * @param strin $rename Nombre con el que se guardará el archivo     
     * @return boolean|array
     */
    public function save($rename='') {
        
        if(!$this->isUploaded()) { //Verifico si está cargado el archivo            
            return FALSE;
        }                
        if (!$this->isWritable()) { //Permite escrituras la ruta?            
            //Los errores son cargados en el método 
            return FALSE;
        }                
        if(!$this->size = $this->isSizeValid()) { //El tamaño es válido?            
            return FALSE;
        }                              
        if (!$this->isAllowedFiletype()) {// Verifico si el tipo de archivo es permitido             
            return FALSE;
        }         
        //Tomo el nomnbre del nuevo archivo
        $this->name = $this->_setFileName($rename);
        //Tomo el archivo temporal
        $file_tmp = $_FILES[$this->file]['tmp_name'];                
        //Verifico si se sube guarda el archivo
        if(move_uploaded_file($file_tmp, "$this->path/$this->name")) {  
            //Verifico si reescala el archivo
            if($this->resize) {
                if(is_file("$this->path/$this->name")) { //Verifico si existe el archivo
                    @chmod("$this->path/$this->name", 0777);
                    try {
                        if($this->removeOld === TRUE) {
                            WideImage::load($this->path.'/'.$this->name)->resize($this->resizeWidth, $this->resizeHeight, 'inside', 'down')->saveToFile($this->path.'/'.$this->name);
                        } else {
                            WideImage::load($this->path.'/'.$this->name)->resize($this->resizeWidth, $this->resizeHeight, 'inside', 'down')->saveToFile($this->path.'/min_'.$this->name);
                            @chmod("$this->path/min_$this->name", 0777);
                        }                           
                    } catch(Exception $e) {
                        $this->setError('Se ha producido un error al intentar reescalar la imagen. <br />Verifica si el archivo es una imagen.');
                        return FALSE;
                    }
                }
            }
            unset($_FILES[$this->file]);
            return array('error'=>false, 'path'=>$this->path, 'name'=>$this->name, 'oldName'=>$this->oldName, 'size'=>$this->_toBytes4Humans($this->size));
        }                
        $this->setError('No se pudo copiar el archivo al servidor. Intenta nuevamente.');
        return FALSE;
                
    }
    
    /**
     * Método para identificar si está cargado el archivo
     * @param string $file
     * @return boolean
     */
    public function isUploaded($file='') {
        $file = (empty($file)) ? $this->file : $file;
        if(! (isset($_FILES[$file]) && is_uploaded_file($_FILES[$file]['tmp_name'])) ) {
            $this->setError('El archivo no se ha podidio cargar en el servidor.');
            return FALSE;
        } 
        //Verifico si el archivo cargado contiene errores
        if ($_FILES[$file]['error'] > 0) {            
            $this->setError('El archivo cargado contiene errores. Intenta nuevamente.');
            return FALSE;
        }        
        return TRUE;
    }
    
    /**
     * Método para verificar si se puede escribir sobre un directorio
     * @param string $path
     * @return boolean
     */
    public function isWritable($path='') {
        $path = empty($path) ? $this->path : $path;
        if(!file_exists($path)) {
            $this->setError('No fué posible ubicar el directorio de carga del archivo.');
            return FALSE;
        }
        if(!is_writable($path)) {            
            $this->setError('El directorio donde se alojará el archivo no tiene permisos de escritura. '.$path);
            return FALSE;
        }  
        return TRUE;
    }
    
    /**
     * Método para verificar el tamaño del archivo
     * @param string $file
     * @return boolean
     */
    public function isSizeValid($file='') {
        $file           = empty($file) ? $this->file : $file;
        $total_bytes    = ($this->maxSize) ? $this->_toBytes($this->maxSize) : 0;        
        $this->size     = $_FILES[$file]['size'];        
        if($this->maxSize !== NULL && ( $this->size > $total_bytes ) ) {
            $this->setError("No se admiten archivos superiores a $this->maxSize");
            return FALSE;
        }
        return $this->size;
    }
    
    /**
     * Método para verificar si el tipo de archivo es permitido
     * @param string $file Nombre del archivo cargado  
     * @return boolean   
     */
    public function isAllowedFiletype($file='') {          
        $file = empty($file) ? $_FILES[$this->file] : $_FILES[$file];
        $ext = $this->getExtension($file['name']);
        //Verifico el tipo permitido
        if($this->allowedTypes == '*') {
            return TRUE;
        }        
        if (count($this->allowedTypes) == 0 OR ! is_array($this->allowedTypes)) {
            $this->setError('No se ha especificado los tipos de archivos permitidos en el servidor.');
            return FALSE;
        }           
        
        //Verifico si la extensión está permitida
        if(!in_array($ext, $this->allowedTypes)) {
            $this->setError('El tipo de archivo subido es incorrecto.');
            return FALSE;
        }
        //Verifico si son imágenes        
        $types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');
        if (in_array($ext, $types)) {
            if (!in_array($file['type'], array('image/jpeg' , 'image/pjpeg' , 'image/gif' , 'image/png'))) {
                $this->setError('Solo se admiten imagenes JPEG, PNG y GIF.');
                return FALSE;
            }
            if (getimagesize($file['tmp_name']) === FALSE) {
                $this->setError('Oops! al parecer la imagen no es correcta.');
                return FALSE;
            }
        }        
        return TRUE;        
    }
    
    /**
     * Método para registrar los tipos de archivo disponibles          
     * @param string $types
     * @return array
     */
    public function setAllowedTypes($types) {
        if ( ! is_array($types) && $types == '*')  {
            $this->allowedTypes = '*';
            return;
        }
        $this->allowedTypes = explode('|', $types);
    }
    
    /**
     * Método para extraer la extension del archivo
     * @param string $filename
     * @return string    
     */
    public function getExtension($filename) {
        $file = explode('.', $filename);        
        $this->file_ext = Filter::get(end($file), 'lower');
        return $this->file_ext;
    }
    
    /**
     * Método para definir si utiliza un md5 como nombre
     * @param boolean $encrypt
     */
    public function setEncryptName($encrypt) {
        $this->encryptName = $encrypt;
    }
    
    /**
     * Método para cambiar el tamaño de la imagen
     * @param string $size Tamaño máximo en del archivo
     * @param numeric $width Ancho de la imagen a reescalar
     * @param numeric $height Alto de la imagen a reescalar
     * @param boolean $removeOld Indica si mantiene la original al reescalar o no. La imagen reescalada mantiene el prefijo min_
     */
    public function setSize($size='2MB', $width=0, $height=0, $removeOld=FALSE) {
        $this->maxSize = $size;
        if($width >0 && $height > 0) {
            $this->resize = TRUE;
            $this->resizeWidth = $width;
            $this->resizeHeight = $height;
            $this->removeOld = ($removeOld === TRUE) ? TRUE : FALSE;
        }
    }

    /**
     * Método para cargar un error
     * @param string $error
     */
    public function setError($error) {
        $this->_error = $error;
    }
    
    /**
     * Método para obtener el error
     * @return string
     */
    public function getError() {
        return $this->_error;
    }
    
    /**
     * Modo para renombrar el archivo
     * @param string $rename
     * @return string
     */
    protected function _setFileName($rename) {        
        if($this->encryptName) {
            $name = md5(uniqid().time()).'.'.$this->file_ext;
            $this->oldName = $_FILES[$this->file]['name'];
        } else {
            $name = empty($rename) ? $_FILES[$this->file]['name'] : $rename.'.'.$this->file_ext;            
        }
        return $name;
    }


    /**
     * Método que devuelve el valor en bytes del archivo
     * @param string $size
     * @return int
     */
    protected function _toBytes($size) {
        if(preg_match('/([KMGTP]?B)/', $size, $matches)) {
            $bytes_array = array('B' => 1, 'KB' => 1024, 'MB' => 1024 * 1024, 'GB' => 1024 * 1024 * 1024, 'TB' => 1024 * 1024 * 1024 * 1024, 'PB' => 1024 * 1024 * 1024 * 1024 * 1024);
            $size = floatval($size) * $bytes_array[$matches[1]];
        }
        return intval(round($size, 2));
    }
    
    /**
     * Método que devuelve el tamaño de un archivo
     * @param string $size
     * @return int
     */
    protected function _toBytes4Humans($size) {
        $base = log($size) / log(1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   
        return round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
    }
}

?>

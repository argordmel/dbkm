<?php
/**
 *
 * Descripcion: Clase que permite el mantenimiento a las tablas de la base de datos
 *
 * @category
 * @package     Models
 * @subpackage 
 */

class Sistema {
    
    /**
     * Variable que contiene las tablas del sistema
     * @var type 
     */
    protected $_tables = array();
    
    /**
     * Varible que contiene la conexión
     */
    protected $_db;
    
    /**
     * Variable con el pull de conexión
     */
    protected $_database;

    /**
     * Método contructor
     */
    public function __construct() {        
        //Reviso la configuración actual
        $config = Config::read('config');
        $this->_database =  $config['application']['database'];
        //Conecto a la bd
        $this->_connect(); 
        //Cargo las tablas
        $this->_loadTables();
    }
    
    /**
     * Se conecta a la base de datos 
     *
     * @param boolean $new_connection
     */
    protected function _connect($new_connection = false) {
        if (!is_object($this->_db) || $new_connection) {
            $this->_db = Db::factory($this->_database, $new_connection);
        }        
    }
    
    /**
     * Método almacenar las tablas
     */
    protected function _loadTables() {
        $tablas = $this->_db->list_tables();
        foreach($tablas as $tabla) {            
            $this->_tables[] = $tabla[0];
        }
    }
    
    /**
     * Método para listar las tablas
     */
    public function getEstadoTablas() {        
        $all_status = array();        
        $tables = $this->_db->fetch_all("SHOW TABLE STATUS"); 
        foreach($tables as $table) {
            $status = $this->_db->fetch_all('CHECK TABLE '.$table['Name']);
            $status = $status[0];
            $table['Op'] = $status['Op'];
            $table['Msg_type'] = $status['Msg_type'];
            $table['Msg_text'] = $status['Msg_text'];            
            $all_status[] = $table;            
        }                
        return $all_status;        
    }
    
    /**
     * Método para desfragmentar una tabla
     */
    public function getDesfragmentacion($tabla) {
        if(in_array($tabla, $this->_tables)) {
            $rs = ($this->_db->query("ALTER TABLE $tabla ENGINE=INNODB"));            
            if($rs) {
                DwAudit::info("Se ha realizado el mantenimiento de desfragmentación a la tabla $tabla");
            } else {
                DwAudit::error("Se ha generado un error al realizar el mantenimiento de desfragmentación a la tabla $tabla");
            }
            return $rs;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para vaciar el cache de una tabla
     */
    public function getVaciadoCache($tabla) {
        if(in_array($tabla, $this->_tables)) {
            $rs = ($this->_db->query("FLUSH TABLE $tabla"));            
            if($rs) {
                DwAudit::info("Se ha realizado el mantenimiento de vaciado del caché a la tabla $tabla");
            } else {
                DwAudit::error("Se ha generado un error al realizar el mantenimiento de vaciado del caché a la tabla $tabla");
            }
            return $rs;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para reparar una tabla
     */
    public function getReparacionTabla($tabla) {
        if(in_array($tabla, $this->_tables)) {
            $rs = ($this->_db->query("REPAIR TABLE $tabla"));
            if($rs) {
                DwAudit::info("Se ha realizado el mantenimiento de reparación a la tabla $tabla");
            } else {
                DwAudit::error("Se ha generado un error al realizar el mantenimiento de reparación a la tabla $tabla");
            }
            return $rs;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para optimizar una tabla
     */    
    public function getOptimizacion($tabla) {
        if(in_array($tabla, $this->_tables)) {
            $rs = ($this->_db->query("OPTIMIZE TABLE $tabla"));            
            if($rs) {
                DwAudit::info("Se ha realizado el mantenimiento de optimización a la tabla $tabla");
            } else {
                DwAudit::error("Se ha generado un error al realizar el mantenimiento de optimización a la tabla $tabla");
            }
            return $rs;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Método para leer los logs del sistema
     */
    public static function getLogger($fecha, $page) {
        DwFile::set_path(APP_PATH . 'temp/logs/');
        $log = DwFile::read('log'.$fecha);
        //Armo un nuevo array para ordenarlos 
        $contador = 0;
        $new_log = array();
        if(!empty($log)) {
            foreach($log as $key => $row) {
                $data = explode(']', $row);
                $new_log[$contador]['item'] = $contador;
                $new_log[$contador]['fecha'] = date("Y-m-d H:i:s", strtotime(trim($data[0],'[')));
                $new_log[$contador]['tipo'] = trim($data[1],'[');
                $new_log[$contador]['descripcion'] = trim($data[2],'[');
                $contador++;
            }                
        }
        $result = DwUtils::orderArray($new_log, 'item', TRUE);                
        //Pagino el array
        $paginate = new DwPaginate();
        return $paginate->paginate($result, "page: $page");
    }
    
    /**
     * Método para leer las autidorías del sistema
     */
    public static function getAudit($fecha, $page=0) { 
        DwFile::set_path(APP_PATH . 'temp/logs/');
        $audit = DwFile::read('audit'.$fecha);
        //Armo un nuevo array para ordenarlos 
        $contador = 0;
        $new_log = array();
        if(!empty($audit)) {
            foreach($audit as $key => $row) {
                $data = explode(']', $row);
                $new_log[$contador]['item'] = $contador;
                $new_log[$contador]['fecha'] = date("Y-m-d H:i:s", strtotime(trim($data[0],'[')));
                $new_log[$contador]['tipo'] = trim($data[1],'[');
                $new_log[$contador]['ruta'] = trim($data[2],'[');
                $new_log[$contador]['usuario'] = trim($data[3],'[');
                $new_log[$contador]['ip'] = trim($data[4],'[');
                $new_log[$contador]['descripcion'] = trim($data[5],'[');
                $contador++;
            }                
        }
        $result = DwUtils::orderArray($new_log, 'item', TRUE);                
        if($page > 0) {
            //Pagino el array
            $paginate = new DwPaginate();
            return $paginate->paginate($result, "page: $page");
        }   
        return $result;
    }
    
    
    /**
     * Método para actualizar el archivo config.ini según los parámetros enviados
     * 
     * @param type $data Campos de los formularios
     * @param type $source Production o Deveploment
     * @param type $createDb Indica si se crea o no la base de datos
     * @return boolean
     */
    public static function setConfig($data, $source='application') {        
        //Verifico si tiene permisos de escritura para crear y editar un archvivo.ini
        if(!is_writable(APP_PATH.'config')) {            
            Flash::warning('Asigna temporalmente el permiso de escritura a la carpeta "config" de tu app!.');
            return false;
        }     
        //Filtro el array
        foreach($data as $key => $val) {
            $data[$key] = Filter::get($val, 'trim');
        }        
        $rs = DwConfig::write('config', $data, $source);        
        if($rs) {
            DwAudit::info('Se ha actualizado el archivo de configuración del sistema');
        }
        return $rs;
    }
    
    /**
     * Método que ajusta el routes.ini 
     * 
     */
    public static function setRoutes($data=null) {
        //Verifico si tiene permisos de escritura para crear y editar un archvivo.ini
        if(!is_writable(APP_PATH.'config')) {            
            Flash::warning('Asigna temporalmente el permiso de escritura a la carpeta "config" de tu app!.');
            return false;
        }  
        if($data==null) {
            //Si está en proceso de instalación
            $data = array('/'=>'home',
                      '/sistema/instalacion/*'=>'delete-var',
                      '/*'=>'delete-var');
        }
        if(empty($data['/'])) {
            $data['/'] = 'home';
        }
        $rs = DwConfig::write('routes', $data, 'routes');
        if($rs) {
            DwAudit::info('Se ha actualizado el archivo de enrutamiento interno del sistema');
        }
        return $rs;
    }
    
    /**
     * Método para crear el archivo databases.ini según los parámetros enviados
     * 
     * @param type $data Campos de los formularios
     * @param type $source Production o Deveploment
     * @param type $createDb Indica si se crea o no la base de datos
     * @return boolean
     */
    public static function setDatabases($data, $source='development') {        
        //Verifico si tiene permisos de escritura para crear y editar un archvivo.ini
        if(!is_writable(APP_PATH.'config')) {            
            Flash::warning('Asigna temporalmente el permiso de escritura a la carpeta "config" de tu app!.');
            return false;
        }     
        //Filtro el array con los parámetros
        $data = Filter::data($data, array('host', 'username', 'password', 'name', 'type', 'charset'), 'trim');        
        $rs =  DwConfig::write('databases', $data, $source);
        if($rs) {
            DwAudit::info('Se ha actualizado el archivo de conexión del sistema');
        }
        return $rs;
    }
    
    /**
     * Método que verifica la conexión con la base de datos
     * @param type $data
     * @param type $source
     * @return boolean 
     */
    public static function testConnection($data, $source, $db=false) {       
        //Filtro el array con los parámetros
        $data = Filter::data($data, array('host', 'username', 'password', 'name', 'type', 'charset'), 'trim');        
        try {           
            //Seteo las variables del core
            Config::set("databases.{$source}", $data);
            //Reviso la conexión, sino, genera la excepción
            @Db::factory($source, true);
            Flash::valid("Conexión establecida en modo <b>$source!</b>");                        
        } catch (KumbiaException $e) {                
            Flash::error("Error en modo '$source': <br /> ".$e->getMessage());
            return false;
        }
    } 
    
    /**
     * Métdo que resetea la configuración del sistema
     * @return boolean
     */
    public static function reset() {
        $files = array('config', 'databases', 'routes', 'install');            
        foreach($files as $name) {
            $file = APP_PATH."config/$name.ini";
            $origin = APP_PATH."config/$name.org.ini";
            if(is_file($file) && is_file($origin)) { //Si hay una copia del archio original
                unlink($file);//Elimino el archivo                
                $org = APP_PATH."config/$name.org.ini";
                $des = APP_PATH."config/$name.ini";
                copy($org, $des);//Copio el original
                @chmod("$des", 0777);//Permisos                                                            
            }
        }            
        DwAudit::info('Se han restaurado los archivos de configuración, enrutamiento y de conexión del sistema');
        //@TODO revisar esto;
        return true;
    }
}
?>

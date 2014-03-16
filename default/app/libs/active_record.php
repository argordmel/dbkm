<?php
/**
 * @see KumbiaActiveRecord
 */
Load::coreLib('kumbia_active_record');

/**
 * ActiveRecord
 *
 * Esta clase es la clase padre de todos los modelos
 * de la aplicacion
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
class ActiveRecord extends KumbiaActiveRecord {
    
    //Se indica si se crear los archivos log
    public $logger = APP_LOGGER;
    
    /**
     * Método que devuelve el order en SQL tomado de la url
     * @param string $s
     * @param string $default Campo por defecto si no se encuentra en el resource
     * @param array $resource Variable que contiene las columnas permitidas y su respectivo ordenamiento
     * @return string
     */
    protected function get_order($s, $default, $resource=array()) {
        $s = explode('.', $s);        
        $column = (empty($s[1])) ? $default : Filter::get($s[1], 'string');        
        $type = (empty($s[2])) ? NULL : strtoupper($s[2]);   
        $type = ($type!='ASC' && $type!='DESC') ? ' ASC' : $type;        
        if(!empty($resource)) {
            //Verifico si están definidas las columnas permitidas para ordenar
            if(array_key_exists($column, $resource)) { //Si está como key (cuando se especifica el order)
                $tmp = $resource[$column];
                $column = (is_array($tmp) && array_key_exists($type, $tmp)) ? $tmp[$type] : $tmp;
                return $column;
            } else if(!in_array($column, $resource)) { //Si no está como valor, se toma el default
                $column = $default;
            }            
        }                
        return $column.' '.$type;        
    }
    
    /**
     * Método para listar resultados de un find_all
     * @return Array ActiveRecord
     */
    public function paginated() {
        $args = func_get_args();
        array_unshift($args, $this);
        require_once APP_PATH . 'libs/dw_paginate.php';
        return call_user_func_array(array('DwPaginate' , 'paginate'), $args);
    }       

    /**
     * Método para listar resultados a través de un sql directo
     * @param string $sql
     * @return Array ActiveRecord
     */
    public function paginated_by_sql($sql) {
        $args = func_get_args();
        array_unshift($args, $this);
        require_once APP_PATH . 'libs/dw_paginate.php';
        return call_user_func_array(array('DwPaginate' , 'paginate_by_sql'), $args);
    }
    
    /**
     * Inicia transacción para cualquier evento
     */
    public static function beginTrans() {
        $obj = new Usuario();
        $obj->begin();
    }
    
    /**
     * Confirma transacción para cualquier evento
     */
    public static function commitTrans() {
        $obj = new Usuario();
        $obj->commit();
    }
    
    /**
     * Cancela transacción para cualquier evento
     */
    public static function rollbackTrans() {
        $obj = new Usuario();
        $obj->rollback();
    }
    
    /**
     * Método para indicar en que sistema operativo se utiliza la base de datos
     * @param boolean $restore
     * @return string
     */
    protected function _getSystem($restore = false) {         
        $sql = $this->sql("SHOW variables WHERE variable_name= 'basedir'");
        $sql = mysqli_fetch_row($sql);
        $base = $sql[1];               
        $raiz = substr($base,0,1);
        if($restore) { //Para restarurar
            $system = ($raiz == '/') ? 'mysql' : $base.'\bin\mysql';
        } else { //Para crear backup
            $system = ($raiz == '/') ? 'mysqldump' : $base.'\bin\mysqldump';
        }        
        return $system;
    }
    
    /**
     * Método para obtener la configuración de conexión que depende del database utilizado
     * @return array
     */
    protected function _getConfig($source) {                  
        $database = Config::read('databases'); //Leo las conexiones existentes                
        $config = $database[$source]; //Extraigo la conexion de la base de datos de la aplicacion        
        return $config;
    }
    
    /**
     * Realiza un conteo de filas
     *
     * @param string $what
     * @return integer
     */
    public function count($what = '') {
        $what = Util::getParams(func_get_args());
        if ($this->schema) {
            $table = "{$this->schema}.{$this->source}";
        } else {
            $table = $this->source;
        }
        unset($what['order']);
        if (isset($what['distinct']) && $what['distinct']) {
            if (isset($what['group'])) {
                $select = "SELECT COUNT(*) FROM (SELECT DISTINCT {$what['distinct']} FROM $table ";
                $select.= $this->convert_params_to_sql($what);
                $select.= ') AS t ';
            } else {
                $select = "SELECT COUNT(DISTINCT {$what['distinct']}) FROM $table ";
                $select.= $this->convert_params_to_sql($what);
            }
        } else {            
            if(isset($what['group']) && isset($what['paginated'])) {                
                $select = "SELECT COUNT(*) FROM (SELECT $table.* FROM $table ";
                $select.= $this->convert_params_to_sql($what);
                $select.=') AS t';
            } else {                
                $select = "SELECT COUNT(*) FROM $table ";
                $select.= $this->convert_params_to_sql($what);
            }
        }                
        $num = $this->db->fetch_one($select);
        return $num[0];
    }

}

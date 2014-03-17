<?php
/**
 *
 * Descripcion: Clase que gestiona los accesos al sistema
 *
 * @category
 * @package     Models
 * @subpackage 
 */

class Acceso extends ActiveRecord {
    
    /**
     * Constante para definir el acceso como entrada
     * @var int
     */
    const ENTRADA = 1;
    
    /**
     * Constante para definir el acceso como salida
     * @var int
     */
    const SALIDA = 2;
       
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('usuario');
    }
    
    /**
     * Método para registrar un acceso
     * @param string $tipo Tipo de acceso acceso/salida
     * @param int $usuario Usuario que accede
     * @param string $ip  Dirección ip
     */
    public static function setAcceso($tipo, $usuario) {
        $usuario            = Filter::get($usuario, 'numeric');        
        $obj                = new Acceso();
        $obj->usuario_id    = $usuario;
        $obj->ip            = DwUtils::getIp();
        $obj->tipo_acceso   = ($tipo==Acceso::ENTRADA) ? 1 : 2;
        $obj->create();
    }     
    
    /**     
     * 
     * Método para listar los accesos de los usuario     
     *       
     * @param int $usuario Identificador del usuario
     * @param string $tipo Tipo de acceso
     * @param string $order Método de ordenamiento
     * @param int $page Número de página
     * @return array ActiveRecord    
     */
    public function getListadoAcceso($usuario=NULL, $tipo='todos', $order='', $page=0) {
        $columns    = 'acceso.*, usuario.login, usuario.nombre, usuario.apellido';
        $join       = 'INNER JOIN usuario ON usuario.id = acceso.usuario_id ';                
        $conditions = (empty($usuario)) ? "usuario.id > '1'" : "usuario.id=$usuario";        
        
        $order = $this->get_order($order, 'acceso.acceso_at', array('fecha'       =>array( 
                                                                        'ASC'=>'acceso.acceso_at ASC, usuario.nombre ASC, usuario.apellido ASC',
                                                                        'DESC'=>'acceso.acceso_at DESC, usuario.nombre ASC, usuario.apellido ASC'), 
                                                                    'nombre'      =>array(
                                                                        'ASC'=>'usuario.nombre ASC, usuario.apellido ASC, acceso.acceso_at DESC', 
                                                                        'DESC'=>'usuario.nombre DESC, usuario.apellido DESC, acceso.acceso_at DESC'),
                                                                    'apellido'    =>array(
                                                                        'ASC'=>'usuario.nombre ASC, usuario.apellido ASC, acceso.acceso_at DESC', 
                                                                        'DESC'=>'usuario.nombre DESC, usuario.apellido DESC, acceso.acceso_at DESC'),
                                                                    'ip',
                                                                    'tipo_acceso'=>array(
                                                                        'ASC'=>'acceso.tipo_acceso ASC, acceso.acceso_at DESC, usuario.nombre ASC, usuario.apellido ASC', 
                                                                        'DESC'=>'acceso.tipo_acceso DESC, acceso.acceso_at DESC, usuario.nombre DESC, usuario.apellido DESC')) );
        
        if($tipo != 'todos') {
            $conditions.= ($tipo!=self::ENTRADA) ? " AND acceso.tipo_acceso = ".self::ENTRADA : " AND acceso.tipo_acceso = ".self::SALIDA;
        } 
        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        } 
        
    }
    
    /**
     * Método para buscar accesos
     * 
     * @param string $field Nombre del campo
     * @param string $value Valor del campo
     * @param string $order Orden
     * @param int $page Número de página
     * @return array ActiveRecord
     */
    public function getAjaxAcceso($field, $value, $order='', $page=0) {
        $value = Filter::get($value, 'string');
        if( strlen($value) <= 2 OR ($value=='none') ) {
            return NULL;
        }
        
        $columns    = 'acceso.*, IF(acceso.tipo_acceso='.self::ENTRADA.', "Entrada", "Salida") AS new_tipo, usuario.login, usuario.nombre, usuario.apellido';
        $join       = 'INNER JOIN usuario ON usuario.id = acceso.usuario_id ';                
        $conditions = "usuario.id > '1'";
        
        $order = $this->get_order($order, 'acceso.acceso_at', array('fecha'       =>array( 
                                                                        'ASC'=>'acceso.acceso_at ASC, usuario.nombre ASC, usuario.apellido ASC',
                                                                        'DESC'=>'acceso.acceso_at DESC, usuario.nombre ASC, usuario.apellido ASC'), 
                                                                    'nombre'      =>array(
                                                                        'ASC'=>'usuario.nombre ASC, usuario.apellido ASC, acceso.acceso_at DESC', 
                                                                        'DESC'=>'usuario.nombre DESC, usuario.apellido DESC, acceso.acceso_at DESC'),
                                                                    'apellido'    =>array(
                                                                        'ASC'=>'usuario.nombre ASC, usuario.apellido ASC, acceso.acceso_at DESC', 
                                                                        'DESC'=>'usuario.nombre DESC, usuario.apellido DESC, acceso.acceso_at DESC'),
                                                                    'ip',
                                                                    'tipo_acceso'=>array(
                                                                        'ASC'=>'acceso.tipo_acceso ASC, acceso.acceso_at DESC, usuario.nombre ASC, usuario.apellido ASC', 
                                                                        'DESC'=>'acceso.tipo_acceso DESC, acceso.acceso_at DESC, usuario.nombre DESC, usuario.apellido DESC')) );
        
        //Defino los campos habilitados para la búsqueda por seguridad
        $fields = array('fecha', 'nombre', 'apellido', 'tipo_acceso',  'ip');
        if(!in_array($field, $fields)) {
            $field = 'nombre';
        }  
        
        if($field=='fecha') {
            $conditions.= " AND DATE(acceso.acceso_at) LIKE '%$value%'";
        } else if($field=='tipo_acceso') {            
            $conditions.= " HAVING new_tipo LIKE '%$value%'";
        } else {
            $conditions.= " AND $field LIKE '%$value%'";
        }
        
        if($page) {
            return $this->paginated_by_sql("SELECT $columns FROM $this->source $join WHERE $conditions ORDER BY $order", "page: $page");
        } else {
            return $this->find_all_by_sql("SELECT $columns FROM $this->source $join WHERE $conditions ORDER BY $order", "order: $order");
        }  
    }
    
}
?>

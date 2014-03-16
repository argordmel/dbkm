<?php
/**
 *
 * Descripcion: Clase que gestiona los perfiles de usuarios
 *
 * @category
 * @package     Models
 */

class Perfil extends ActiveRecord {
    
    //Se desabilita el logger para no llenar el archivo de "basura"
    public $logger = FALSE;
    
    /**
     * Constante para definir el perfil de Super Usuario
     */
    const SUPER_USUARIO = 1;
    
    /**
     * Constante para definir un perfil como activo
     */
    const ACTIVO = 1;
    
    /**
     * Constante para definir un perfil como inactivo
     */
    const INACTIVO = 2;
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->has_many('usuario');
        $this->has_many('recurso_perfil');
    }
    
    /**
     * Método para obtener el listado de los perfiles del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoPerfil($estado='todos', $order='', $page=0) {                   
        $columns = 'perfil.*, COUNT(usuario.id) AS usuarios';        
        $join = 'LEFT JOIN usuario ON perfil.id = usuario.perfil_id ';
        $conditions = 'perfil.id IS NOT NULL';        
        if($estado=='acl') {
            $conditions.= " AND perfil.estado = ".self::ACTIVO;
        } else if($estado=='mi_cuenta') {
            $conditions.= " AND estado=".self::ACTIVO;
            $conditions.= (Session::get('perfil_id') == Perfil::SUPER_USUARIO) ? '' : " AND perfil.id > 1";
        } else {
            $conditions.= " AND perfil.id > 1";            
            if($estado!='todos') {
                $conditions.= ($estado==self::ACTIVO) ? " AND estado=".self::ACTIVO : " AND estado=".self::INACTIVO;                
            }
        }        
        $order = $this->get_order($order, 'perfil', array(            
            'usuarios' => array(
                'ASC' => 'usuarios ASC, perfil.perfil ASC',
                'DESC' => 'usuarios DESC, perfil.perfil DESC'
            ),
            'estado' => array(
                'ASC' => 'perfil.estado ASC, perfil.perfil ASC',
                'DESC' => 'perfil.estado DESC, perfil.perfil DESC'
            ),
            'plantilla' => array(
                'ASC' => 'perfil.plantilla ASC, perfil.perfil ASC',
                'DESC' => 'perfil.plantilla DESC, perfil.perfil DESC'
            )
        ));
        $group = 'perfil.id';
        if($page) {            
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order", "page: $page");
        }
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
    }
    
    /**
     * Método para crear/modificar un objeto de base de datos
     * 
     * @param string $medthod: create, update
     * @param array $data: Data para autocargar el modelo
     * @param array $optData: Data adicional para autocargar
     * 
     * return object ActiveRecord
     */
    public static function setPerfil($method, $data, $optData=null) {        
        $obj = new Perfil($data); //Se carga los datos con los de las tablas        
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }             
        //Verifico que no exista otro perfil, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "perfil = '$obj->perfil'" : "perfil = '$obj->perfil' AND id != '$obj->id'";
        $old = new Perfil();
        if($old->find_first($conditions)) {            
            //Si existe y se intenta crear pero si no se encuentra activo lo activa
            if($method=='create' && $old->estado != Perfil::ACTIVO) {
                $obj->id        = $old->id;
                $obj->estado    = Perfil::ACTIVO;
                $method         = 'update';
            } else {
                Flash::info('Ya existe un perfil registrado bajo ese nombre.');
                return FALSE;
            }
        }
        return ($obj->$method()) ? $obj : FALSE;
    }
    
    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->perfil       = Filter::get($this->perfil, 'string');
        $this->plantilla    = Filter::get($this->plantilla, 'string');
        $this->plantilla    = (!empty($this->plantilla)) ? DwUtils::getSlug($this->plantilla, '_') : 'default';
        if(!empty($this->id)) {
            if($this->id == Perfil::SUPER_USUARIO) {
                Flash::warning('Lo sentimos, pero este perfil no se puede editar.');
                return 'cancel';
            }
        }
        $path = APP_PATH.'views/_shared/templates/backend/'.$this->plantilla.'.phtml';
        //Verifico si se encuentra el template
        if(!is_file($path)) {
            Flash::error('Lo sentimos, pero no hemos podidio ubicar la plantilla '.$this->plantilla); 
            return 'cancel';
        }
    }
    
    /**
     * Callback que se ejecuta después de guardar/modificar un perfil
     */
    protected function after_save() {
        $data = array();
        $data[] = Recurso::DASHBOARD.'-'.$this->id;
        if(!RecursoPerfil::setRecursoPerfil($data)) {
            Flash::info("No se ha podido establcer el recurso 'dashboard' preestablecido al perfil.");
            return 'cancel';
        }
        $data = array();
        $data[] = Recurso::MI_CUENTA.'-'.$this->id;
        if(!RecursoPerfil::setRecursoPerfil($data)) {
            Flash::info("No se ha podido establcer el recurso 'Mi Cuenta' preestablecido al perfil.");
            return 'cancel';
        }
    }


    /**
     * Método para obtener los ecursos de un perfil
     * @param type $perfil
     * @return type
     */
    public function getRecursos($perfil){        
        $columnas = "recurso.*";
        $join = "INNER JOIN recurso_perfil ON perfil.id = recurso_perfil.perfil_id ";
        $join.= "INNER JOIN recurso ON recurso.id = recurso_perfil.recurso_id ";
        $conditions = "perfil.id = '$perfil'";
        return $this->find("columns: $columnas" , "join: $join", "conditions: $conditions");
    }
    
}
?>

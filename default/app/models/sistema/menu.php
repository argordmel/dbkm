<?php
/**
 *
 * Clase que gestiona los menús de los usuarios según los recursos asignados
 *
 * @category
 * @package     Models
 * @subpackage
 */

class Menu extends ActiveRecord {

    //Se desabilita el logger para no llenar el archivo de "basura"
    public $logger = TRUE;

    /**
     * Constante para definir un menú como activo
     */
    const ACTIVO = 1;

    /**
     * Constante para definir un menú como inactivo
     */
    const INACTIVO = 2;

    /**
     * Constante para definir un menú visible en el backend
     */
    const BACKEND = 1;

    /**
     * Constante para definir un menú visible en el frontend
     */
    const FRONTEND = 2;

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->has_many('menu');
        $this->belongs_to('recurso');
    }

    /**
     * Método para obtener los menús padres, por entorno o perfil
     */
    public function getListadoMenuPadres($entorno='', $perfil='') {
        if($entorno == Menu::FRONTEND) {
            $columns = 'menu.*';
            $conditions = "menu.menu_id IS NULL AND menu.visibilidad = $entorno AND menu.activo = ".self::ACTIVO;
            $group = 'menu.id';
            $order = 'menu.posicion ASC';
            return $this->find("columns: $columns", "conditions: $conditions", "group: $group", "order: $order");
        } else {

            $columns = 'padre.*';
            $join = 'INNER JOIN menu AS padre ON padre.id = menu.menu_id ';
            $conditions = "padre.menu_id IS NULL";
            if($entorno) {
                $join.= 'LEFT JOIN recurso ON recurso.id = menu.recurso_id ';
                $join.= 'LEFT JOIN recurso_perfil ON recurso.id = recurso_perfil.recurso_id ';
                $conditions.= " AND padre.visibilidad = $entorno AND padre.activo = ".self::ACTIVO;
            }
            if(!empty($perfil)) {
                //Verifico si el perfil tiene el comodín
                $recurso = new RecursoPerfil();
                if($recurso->count("recurso_id = ".Recurso::COMODIN." AND perfil_id= $perfil")) {
                    $perfil = NULL; //Para que liste todos los menús
                }
                $conditions.= (empty($perfil) OR $perfil==Perfil::SUPER_USUARIO) ? '' : " AND recurso_perfil.perfil_id = $perfil";
            }
            $group = 'padre.id';
            $order = 'padre.posicion ASC';
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
        }
    }

    /**
     * Método para obtener los submenús de cada menú según el perfil
     */
    public function getListadoSubmenu($entorno, $menu, $perfil='') {
        $columns = 'menu.*';
        $join = 'LEFT JOIN recurso ON recurso.id = menu.recurso_id ';
        $join.= 'LEFT JOIN recurso_perfil ON recurso.id = recurso_perfil.recurso_id ';
        $conditions = "menu.menu_id = $menu AND menu.visibilidad = $entorno AND menu.activo = ".self::ACTIVO;
        if($perfil) {
            //Verifico si el perfil tiene el comodín
            $recurso = new RecursoPerfil();
            if($recurso->count("recurso_id = ".Recurso::COMODIN." AND perfil_id= $perfil")) {
                $perfil = NULL; //Para que liste todos los submenús
            }
            $conditions.= (empty($perfil) OR $perfil==Perfil::SUPER_USUARIO) ? '' :  " AND recurso_perfil.perfil_id = $perfil";
        }
        $group = 'menu.id';
        $order = 'menu.posicion ASC';
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
    }

    /**
     * Método para obtener los menús padres
     */
    public function getMenusPorPadre($padre, $order) {
        $columns = 'menu.*, (padre.menu) AS padre, (padre.posicion) AS padre_posicion, recurso.recurso';
        $join = 'LEFT JOIN recurso ON recurso.id = menu.recurso_id ';
        $join.= 'LEFT JOIN menu AS padre ON padre.id = menu.menu_id ';
        $conditions = "menu.menu_id = $padre";
        $group = 'menu.id';
        $order = $this->get_order($order, 'padre_posicion', array(
            'posicion' => array(
                'ASC'  => 'menu.posicion ASC',
                'DESC' => 'menu.posicion DESC'
            ),
            'padre' => array(
                'ASC'  => 'padre ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'menu' => array(
                'ASC'  => 'padre ASC, menu ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'visibilidad' => array(
                'ASC'  => 'padre.visibilidad ASC, menu.visibilidad ASC, menu ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre.visibilidad DESC, menu.visibilidad DESC, padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'activo' => array(
                'ASC'  => 'menu.activo ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'menu.activo DESC, menu.visibilidad DESC, padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            )
        ));
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "group: $group", "order: $order");
    }

    /**
     * Método para obtener el listado de los menús del sistema
     * @param type $estado
     * @param type $order
     * @param type $page
     * @return type
     */
    public function getListadoMenu($estado='todos', $order='', $page=0) {
        $columns = 'menu.*, (padre.menu) AS padre, (padre.posicion) AS padre_posicion, recurso.recurso';
        $join = 'LEFT JOIN recurso ON recurso.id = menu.recurso_id ';
        $join.= 'LEFT JOIN menu AS padre ON padre.id = menu.menu_id ';
        $conditions = 'menu.id IS NOT NULL';
        if($estado!='todos') {
            $conditions.= ($estado==self::ACTIVO) ? " AND menu.activo=".self::ACTIVO : " AND menu.activo=".self::INACTIVO;
        }

        $order = $this->get_order($order, 'padre_posicion', array(
            'posicion' => array(
                'ASC'  => 'padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre_posicion DESC, menu.posicion DESC'
            ),
            'padre' => array(
                'ASC'  => 'padre ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'menu' => array(
                'ASC'  => 'padre ASC, menu ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'visibilidad' => array(
                'ASC'  => 'padre.visibilidad ASC, menu.visibilidad ASC, menu ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'padre.visibilidad DESC, menu.visibilidad DESC, padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            ),
            'activo' => array(
                'ASC'  => 'menu.activo ASC, padre_posicion ASC, menu.posicion ASC',
                'DESC' => 'menu.activo DESC, menu.visibilidad DESC, padre DESC, menu DESC, padre_posicion DESC, menu.posicion DESC'
            )
        ));

        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page");
        }
        return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
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
    public static function setMenu($method, $data, $optData=null) {
        $obj = new Menu($data); //Se carga los datos con los de las tablas
        if($optData) { //Se carga información adicional al objeto
            $obj->dump_result_self($optData);
        }
        //Verifico que no exista otro menu, y si se encuentra inactivo lo active
        $conditions = empty($obj->id) ? "recurso_id='$obj->recurso_id' AND visibilidad=$obj->visibilidad" : "recurso_id='$obj->recurso_id' AND visibilidad=$obj->visibilidad AND id != '$obj->id'";
        $old = new Menu();
        if($old->find_first($conditions)) {
            if($method=='create' && $old->activo != Menu::ACTIVO) {
                $obj->id = $old->id;
                $obj->activo = Menu::ACTIVO;
                $method = 'update';
            } else {
                Flash::info('Ya existe un menú registrado para ese recurso y visibilidad.');
                return FALSE;
            }
        }

        $obj->custom = (Session::get('perfil_id') == Perfil::SUPER_USUARIO) ? 0 : 1;

        return ($obj->$method()) ? $obj : FALSE;
    }

    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
        $this->menu     = Filter::get($this->menu, 'string');
        $this->url      = Filter::get($this->url, 'string');
        if(empty($this->url)) {
            $this->url  = '#';
        }
        $this->icono    = Filter::get($this->icono, 'string');
        $this->posicion = Filter::get($this->posicion, 'int');

        if(!empty($this->id) && ($this->id <= 2) ) { //Para no editar el dashboard
            Flash::warning('Lo sentimos, pero este menú no se puede editar.');
            return 'cancel';
        }
    }

    /**
     * Método para obtener los menús padres para edición
     */
    public function getListadoEdicion($entorno) {
        $conditions = "menu.menu_id IS NULL AND menu.visibilidad = $entorno AND menu.activo = ".self::ACTIVO;
        $group      = 'menu.id';
        $order      = 'menu.posicion ASC';
        return $this->find( "conditions: $conditions", "group: $group", "order: $order");
    }

}
?>
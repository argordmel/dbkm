<?php
/** 
 *
 * Clase que gestiona todo lo relacionado con los
 * recursos de los usuarios con su respectivo grupo
 *
 * @category
 * @package     Models 
 */

class RecursoPerfil extends ActiveRecord {
    
    //Se desabilita el logger para no llenar el archivo de "basura"
    public $logger = FALSE;
        
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
        $this->belongs_to('recurso');
        $this->belongs_to('usuario');
    }

    /**
     * Método que retorna los recursos asignados a un perfil de usuario
     * @param int $perfil Identificador el perfil del usuario
     * @return array object ActieRecord
     */
    public function getRecursoPerfil($perfil) {
        $perfil     = Filter::get($perfil,'numeric');
        $columnas   = 'recurso_perfil.*, recurso.modulo, recurso.controlador, recurso.accion, recurso.descripcion, recurso.estado';
        $join       = 'INNER JOIN recurso ON recurso.id = recurso_perfil.recurso_id';        
        $condicion  = "recurso_perfil.perfil_id = '$perfil'";
        $order      = 'recurso.modulo ASC, recurso.controlador ASC,  recurso.recurso_at ASC';        
        return $this->find("columns: $columnas", "join: $join", "conditions: $condicion", "order: $order");        
    }
    
    /**
     * Método para listar los privilegios y compararlos con los recursos y perfiles
     * @return array
     */
    public function getPrivilegiosToArray() {
        $data = array();
        $privilegios = $this->find();
        foreach($privilegios as $privilegio) {
            $data[] = $privilegio->recurso_id.'-'.$privilegio->perfil_id;
        }        
        return $data;
    }
    
    
    /**
     * Método para registrar los privilegios a los perfiles
     */
    public static function setRecursoPerfil($privilegios, $old_privilegios=NULL) {
        $obj = new RecursoPerfil();
        $obj->begin();
        //Elimino los antiguos privilegios
        if(!empty($old_privilegios)) {
            $items = explode(',', $old_privilegios);
            foreach($items as $value) {
                $data = explode('-', $value); //el formato es 1-4 = recurso-rol
                if($data[0] != Recurso::DASHBOARD && $data[0] != Recurso::MI_CUENTA) { //Para que no elimine el principal y mi cuenta
                    if(!$obj->delete("recurso_id = $data[0] AND perfil_id = $data[1]")){                    
                        $obj->rollback();
                        return FALSE;
                    }                
                }
            }                        
        } 
        if(!empty($privilegios)) {
            foreach($privilegios as $value) {                 
                $data = explode('-', $value); //el formato es 1-4 = recurso_id-perfil_id
                $obj->recurso_id = $data[0];
                $obj->perfil_id = $data[1];
                if($obj->exists("recurso_id=$obj->recurso_id AND perfil_id=$obj->perfil_id")){
                    continue;
                }
                if(!$obj->create()) {            
                    $obj->rollback();
                    return FALSE;
                }
            }
        }
        $obj->commit();
        return TRUE;
    }
    
}
?>
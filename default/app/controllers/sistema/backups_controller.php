<?php
/**
 *
 * Descripcion: Controlador que se encarga de la gestión de las copias de seguridad del sistema
 *
 * @category    
 * @package     Controllers  
 */

Load::models('sistema/backup');

class BackupsController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Backups';
    }
    
    /**
     * Método principal
     */
    public function index() {
        Redirect::toAction('listar');
    }
    
    /**
     * Método para buscar
     */
    public function buscar($field='denominacion', $value='none', $order='order.id.asc', $page=1) {        
        $page               = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field              = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value              = (Input::hasPost('field')) ? Input::post('value') : $value;
        
        $backup             = new Backup();
        $backups            = $backup->getAjaxBackup($field, $value, $order, $page);        
        if(empty($backups->items)) {
            Flash::info('No se han encontrado registros');
        }
        
        $this->backups      = $backups;
        $this->order        = $order;
        $this->field        = $field;
        $this->value        = $value;
        $this->page_title   = 'Búsqueda de copias de seguridad';        
    }
    
    /**
     * Método para listar
     */
    public function listar($order='order.id.desc', $page='page.1') { 
        $page               = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $backup             = new Backup();
        $backups            = $backup->getListadoBackup($order, $page);
        if(empty($backups->items)) {
            Flash::warning("Por favor realiza una copia de seguridad lo antes posible.");
        }
        $this->backups      = $backups;
        $this->order        = $order;        
        $this->page_title   = 'Listado de copias de seguridad';
    }
    
    /**
     * Método para crear
     */
    public function crear() {
        if(Input::hasPost('backup')) {
            if($backup = Backup::createBackup(Input::post('backup'))) {                
                Flash::valid('Se ha realizado una nueva copia de seguridad bajo el archivo <b>'.$backup->archivo.' </b> correctamente.');
                return Redirect::toAction('listar');
            }
        }
        $this->page_title = 'Crear copia de seguridad';
    }
    
    /**
     * Método para restaurar
     */
    public function restaurar($key='') {                  
        if(!Input::isAjax()) {
            Flash::error('Método incorrecto para restaurar el sistema.');
            return Redirect::toAction('listar');
        }        
        if(!$id = Security::getKey($key, 'restaurar_backup', 'int')) {
            return View::ajax();
        }        
        $pass       = Input::post('password');
        $usuario    = Usuario::getUsuarioLogueado();
        if($usuario->password != sha1($pass)) {
            Flash::error('Acceso incorrecto al sistema. Tu no tienes los permisos necesarios para realizar esta acción.');
            return View::ajax();
        }        
        if($backup = Backup::restoreBackup($id)) {
            Flash::valid('El sistema se ha restaurado satisfactoriamente con la copia de seguridad <b>'.$backup->archivo.'</b>');
        } else {
            Flash::error('Se ha producido un error interno al restaurar el sistema. Por favor contacta al administrador.');
        }        
        return View::ajax();
        
    }
    
    /**
     * Método para descargar
     */
    public function descargar($key='') {
        
        if(!$id = Security::getKey($key, 'descargar_backup', 'int')) {
            return Redirect::toAction('listar');
        }        
        
        $backup = new Backup();
        if(!$backup->find_first($id)) {
            Flash::info('La copia de seguridad no se encuentra registrada en la base de datos');
            return Redirect::toAction('listar');
        }        
        
        $file = APP_PATH . 'temp/backup/'.$backup->archivo;
        if(!is_file($file)) {
            Flash::warning('No hemos podido localizar el archivo. Por favor contacta al administrador del sistema.');
            DwAudit::error("No se ha podido encontrar la copia de seguridad $backup->archivo en el sistema");
            return Redirect::toAction('listar');
        }
        
        View::template(NULL);
        
        $this->backup = $backup;
    }
    
}


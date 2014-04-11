<?php
/**
 * Descripcion: Controlador que se encarga de la gestión de la configuración del sistema
 *
 * @category
 * @package     Controllers 
 */

Load::models('sistema/sistema');

class ConfiguracionController extends BackendController {

    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_title = 'Configuración del sistema';
    }

    /**
     * Método principal para las configuraciones básicas
     */
    public function index() {
        if(Input::hasPost('application') && Input::hasPost('custom')) {
            try {
                Sistema::setConfig(Input::post('application'), 'application');
                Sistema::setConfig(Input::post('custom'), 'custom');
                Flash::valid('El archivo de configuración se ha actualizado correctamente!');
            } catch(KumbiaException $e) {
                Flash::error('Oops!. Se ha realizado algo mal internamente. <br />Intentalo de nuevo!.');
            }
            Input::delete('application');
            Input::delete('custom');
        }
        $this->config = DwConfig::read('config', '', true);
        $this->page_module = 'Configuración general';
    }

    /**
     * Método para todas las configuraciones
     */
    public function config() {
        if(Input::hasPost('application') && Input::hasPost('custom')) {
            try {
                Sistema::setConfig(Input::post('application'), 'application');
                Sistema::setConfig(Input::post('custom'), 'custom');
                Flash::valid('El archivo de configuración se ha actualizado correctamente!');
            } catch(KumbiaException $e) {
                Flash::error('Oops!. Se ha realizado algo mal internamente. <br />Intentalo de nuevo!.');
            }
            Input::delete('application');
            Input::delete('custom');
        }
        $this->config = DwConfig::read('config', '', true);
        $this->page_module = 'Configuración general';
    }

    /**
     * Método para editar el routes
     */
    public function routes() {
        if(Input::hasPost('routes')) {
            try {
                Sistema::setRoutes(Input::post('routes'));
                Flash::valid('El archivo de enrutamiento se ha actualizado correctamente!');
            } catch(KumbiaException $e) {
                Flash::error('Oops!. Se ha realizado algo mal internamente. <br />Intentalo de nuevo!.');
            }
            Input::delete('routes');
            $_POST = array();
        }
        $this->routes = DwConfig::read('routes', '', true);
        $this->page_module = 'Configuración de enrutamientos';
    }

    /**
     * Método para editar el databases
     */
    public function databases() {
        if(Input::hasPost('development') && Input::hasPost('production')) {
            try {
                Sistema::setDatabases(Input::post('development'), 'development');
                Sistema::setDatabases(Input::post('production'), 'production');
                Flash::valid('El archivo de conexión se ha actualizado correctamente!');
            } catch(KumbiaException $e) {
                Flash::error('Oops!. Se ha realizado algo mal internamente. <br />Intentalo de nuevo!.');
            }
            Input::delete('databases');
        }
        $this->databases = DwConfig::read('databases', '', true);
        $this->page_module = 'Configuración de conexión';
    }

    /**
     * Método para verificar la conexión de la bd
     */
    public function test() {
        if(!Input::isAjax()) {
            Flash::error('Acceso incorrecto para la verificación del sistema.');
            return Redirect::toRoute('module: dashboard', 'controller: index');
        }
        if(!Input::hasPost('development') OR !(Input::hasPost('production')) ) {
            Flash::error('Oops!. No hemos recibido algún parámetro de configuración.');
        } else {
            if(Input::hasPost('development')) {
                Sistema::testConnection(Input::post('development'), 'development', true);
            }
            If(Input::hasPost('production')) {
                Sistema::testConnection(Input::post('production'), 'production', true);
            }
        }
        View::ajax();
    }

    /**
     * Método para resetear las configuraciones del sistema
     * @return type
     */
    public function reset() {
        try {
            if(Sistema::reset()) {
                Flash::valid('El sistema se ha reseteado correctamente!');
            }
        } catch(KumbiaException $e) {
            Flash::error('Se ha producido un error al resetear la configuración del sistema.');
        }
        return Redirect::toAction('index');
    }
}


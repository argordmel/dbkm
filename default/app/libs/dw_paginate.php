<?php
/**
 *
 * Método para paginar resultados de la base de datos
 *
 * @category    Paginación
 * @package     Libs
 */

class DwPaginate {

    /**
     * Método para paginar resultados utilizando el método find de los modelos <br>
     *
     * Retorna un PageObject que tiene los siguientes atributos: <br>
     * next: numero de pagina siguiente, si no hay pagina siguiente entonces es false <br>
     * prev: numero de pagina anterior, si no hay pagina anterior entonces es false <br>
     * current: numero de pagina actual <br>
     * total: total de paginas que se pueden mostrar <br>
     * items: array de items de la pagina <br>
     * counter: Número que lleva el conteo de la página <br>
     * size: Total de registros <br>
     * per_page: cantidad de elementos por pagina <br>
     *
     *
     * @param array $model
     * @return stdClass 
     */
    public static function paginate($model) {
                
        $params = Util::getParams(func_get_args($model));
        $page_number = isset($params['page']) ? Filter::get($params['page'], 'numeric') : 1; //Numero de la página       
        $per_page = isset($params['per_page']) ? Filter::get($params['per_page'], 'numeric') : DATAGRID; //Datos por página        
        $counter = ($page_number > 1) ? ( ($page_number * $per_page) - ($per_page-1) ) : 1; //Determino el contador para utilizarlo en la vista       
        
        $start = $per_page * ($page_number - 1); //Determino el offset      
        $page = new stdClass(); //Instancia del objeto contenedor de pagina        
        
        //Si es un array, se hace páginacion de array
        if (is_array($model)) {
            $items = $model;
            $n = count($items);
            //si el inicio es superior o igual al conteo de elementos,
            //entonces la página no existe, exceptuando cuando es la página 1
            if ($page_number > 1 && $start >= $n) {
                $url = Router::get('route');
                $url = explode('pag', $url);
                $url = trim($url[0],'/');
                Flash::error('La página solicitada no se encuentra en el paginador.  <br />'.DwHtml::link($url, 'Regresar a la página 1'));
            }
            $total_items = $n;
            $page->items = array_slice($items, $start, $per_page);
        } else {
            $find_args = array(); //Arreglo que contiene los argumentos para el find        
            $conditions = null;
             //Asignando parametros de busqueda        
            if (isset($params['conditions'])) {            
                $conditions = $params['conditions'];
            } else if (isset($params[1])) {         
                $conditions = $params[1];
            }
            if (isset($params['columns'])) {            
                $find_args[] = "columns: {$params['columns']}";
            }
            if (isset($params['join'])) {            
                $find_args[] = "join: {$params['join']}";
            }
            if (isset($params['group'])) {            
                $find_args[] = "group: {$params['group']}";
            }
            if (isset($params['having'])) {            
                $find_args[] = "having: {$params['having']}";
            }
            if (isset($params['order'])) {            
                $find_args[] = "order: {$params['order']}";
            }
            if (isset($params['distinct'])) {            
                $find_args[] = "distinct: {$params['distinct']}";
            }
            
            //Count by paginated
            $find_args[] = "paginated: ".true;
            
            if (isset($conditions)) {            
                $find_args[] = $conditions;
            }
            $total_items = call_user_func_array(array($model , 'count'), $find_args); //Se cuentan los registros
            $find_args[] = "offset: $start"; //Asignamos el offset
            $find_args[] = "limit: $per_page"; //Asignamos el limit               
            $page->items = call_user_func_array(array($model , 'find'), $find_args); //Se efectua la busqueda            
        }
        
        //Se efectuan los cálculos para las paginas
        $page->next = ($start + $per_page) < $total_items ? ($page_number + 1) : false;
        $page->prev = ($page_number > 1) ? ($page_number - 1) : false;
        $page->current = $page_number;
        $page->total_page = ceil($total_items / $per_page);        
        if( ($page->total_page < $page_number) && ($total_items > 0)){
            $page->prev = false;
            $url = Router::get('route');
            $url = explode('pag', $url);
            $url = trim($url[0],'/');
            Flash::error('La página solicitada no se encuentra en el paginador.  <br />'.DwHtml::link($url, 'Regresar a la página 1'));
        }
        $page->counter = ($total_items >= $counter) ? $counter : 1;
        $page->size = $total_items;
        $page->per_page = $per_page;
        
        return $page;        
    }

    /**
     * Método para paginar resultados utilizando el método find_all_by_sql de los modelos <br>
     *
     * Retorna un PageObject que tiene los siguientes atributos: <br>
     * next: numero de pagina siguiente, si no hay pagina siguiente entonces es false <br>
     * prev: numero de pagina anterior, si no hay pagina anterior entonces es false <br>
     * current: numero de pagina actual <br>
     * total: total de paginas que se pueden mostrar <br>
     * items: array de items de la pagina <br>
     * counter: Número que lleva el conteo de la página <br>
     * size: Total de registros <br>
     * per_page: cantidad de elementos por pagina <br>
     *
     *
     * @param string $model modelo
     * @param string $sql consulta sql
     * @return stdClass
     */
    public static function paginate_by_sql($model, $sql) {

        $params = Util::getParams(func_get_args());
        $page_number = isset($params['page']) ? Filter::get($params['page'], 'numeric') : 1; //Numero de la página
        $per_page = isset($params['per_page']) ? Filter::get($params['per_page'], 'numeric') : DATAGRID; //Datos por página
        $counter = ($page_number > 1) ? ( ($page_number * $per_page) - ($per_page-1) ) : 1; //Determino el contador para utilizarlo en la vista

        $start = $per_page * ($page_number - 1); //Determino el offset
        $page = new stdClass(); //Instancia del objeto contenedor de pagina

        $total_items = $model->count_by_sql("SELECT COUNT(*) FROM ($sql) AS t");//Se cuentan los registros
        $page->items = $model->find_all_by_sql($model->limit($sql, "offset: $start", "limit: $per_page")); //Se efectua la búsqueda

        //Se efectuan los cálculos para las paginas
        $page->next = ($start + $per_page) < $total_items ? ($page_number + 1) : false;
        $page->prev = ($page_number > 1) ? ($page_number - 1) : false;
        $page->current = $page_number;
        $page->total_page = ceil($total_items / $per_page);        
        if( ($page->total_page < $page_number) && ($total_items > 0)){            
            $page->prev = false;
            $url = Router::get('route');
            $url = explode('pag', $url);
            $url = trim($url[0],'/');
            Flash::error('La página solicitada no se encuentra en el paginador.  <br />'.DwHtml::link($url, 'Regresar a la página 1'));
        }
        $page->counter = ($total_items >= $counter) ? $counter : 1;
        $page->size = $total_items;
        $page->per_page = $per_page;

        return $page;
    }
}
?>

<?php
/**
 *
 * Extension para el manejo de javascript
 *
 * @category    Helpers
 * @package     Helpers
 */

class DwJs {
    
    /**
     * Contador de dialogos
     * @var int
     */
    protected static $_counter = 1;
    
    /**
     * Abre una etiqueta para javascript
     * @return string
     */
    public static function open() {
        return '<script type="text/javascript">'.PHP_EOL;
    }

    /**
     * Cierra una etiqueta de código javascript
     * @return string
     */
    public static function close() {
        return '</script>'.PHP_EOL;
    }
    
    /**
     * Método para generar un mensaje de alerta, párametros que puede recibir: "icon: icono", "title: ", "subtext: ", "name: ", "autoOpen: "
     * @param type $text
     * @param type $params
     * @return type
     */
    public static function alert($text, $params='') {
        //Extraigo los parametros
        $params     = Util::getParams(func_get_args());
        $icon       = (isset($params['icon'])) ? $params['icon'] : 'fa-exclamation-sign';
        $title      = isset($params['title']) ? '<i class="'.$icon.'" style="padding-right:5px; margin-top:5px;"></i>'.$params['title'] : null;
        $subtext    = isset($params['subtext']) ? "<p style='margin-top: 10px'>{$params['subtext']}</p>" : null;
        $name       = isset($params['name']) ? trim($params['name'],'()') : "dwModal".rand(10, 5000);
        $autoOpen   = (isset($params['autoOpen'])) ? true : false;
        $button     = isset($params['show_button']) && Filter::get($params['show_button'], 'lower') == 'false' ? false : true;
        $style      = isset($params['style']) ? $params['style'] : ''; 
        
        $modal = '<div class="modal fade" tabindex="-1" id="'.$name.'" role="dialog" aria-labelledby="'.$name.'" aria-hidden="true">';
            $modal.= '<div class="modal-dialog" style="'.$style.'">';
                $modal.= '<div class="modal-content">';
                    $modal.= '<div class="modal-header">';
                    $modal.= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                    $modal.= ($title) ? "<h4 class=\"modal-title\">$title</h4>" : '';
                    $modal.= '</div>';
                    $modal.= "<div class=\"modal-body\">$text $subtext</div>";
                    if($button) {
                        $modal.= '<div class="modal-footer">';
                            $modal.= '<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Aceptar</button>';
                        $modal.= '</div>';
                    }                    
                $modal.= '</div>';
            $modal.= '</div>';
        $modal.= '</div>';
        $modal.= self::open();
        $modal.= "function $name() { $('#$name').modal('show'); }; ";
        if($autoOpen) {
            $modal.='$(function(){ '.$name.'(); });';
        }
        $modal.= "$('#$name').on('shown.bs.modal', function () { $('.btn-primary', '#$name').focus(); });";
        $modal.= self::close();
        return $modal;
    }
    
    /**
     * Método para modificar la url despues de enrutar
     * @param type $url
     * @return string 
     */
    public static function updateUrl($url) {
        $url = trim($url, '/').'/';
        $js = self::open();
        $js.= "updateUrl('$url');";        
        $js.= self::close();        
        return $js;
    } 
           
}

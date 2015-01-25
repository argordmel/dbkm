<?php
/**
 *
 * Extension para el manejo de algunas etiquetas html
 *
 * @category    Helpers
 * @package     Helpers
 */

class DwHtml extends Html {

    /**
     * Método para genera un link con ícono
     * @param string $action
     * @param type $text
     * @param type $attrs
     * @param type $icon
     * @param type $loadAjax
     * @return type
     */
    public static function link ($action, $text, $attrs = NULL, $icon='', $loadAjax = APP_AJAX) {
        if (is_array($attrs) OR empty($attrs)) {
            if($loadAjax) {
                if(empty($attrs['class'])) {
                    $attrs['class'] = 'js-link js-spinner js-url';
                } else {
                    if(!preg_match("/\bno-ajax\b/i", $attrs['class'])) {
                        $attrs['class'] = 'js-link '.$attrs['class'];
                    }
                    if(!preg_match("/\bno-spinner\b/i", $attrs['class'])) {
                        $attrs['class'] = 'js-spinner '.$attrs['class'];
                    }
                    if(!preg_match("/\bno-url\b/i", $attrs['class'])) {
                        $attrs['class'] = 'js-url '.$attrs['class'];
                    }
                }
            }
            if(!empty($attrs)) {
                $attrs = Tag::getAttrs($attrs);
            }
        }
        if(empty($action)) {
            $action = PUBLIC_PATH;
        } else {
            if(!preg_match('/^(http|ftp|https)\:\/\/+[a-z0-9\.\_-]+$/i', $action)) {
                $extensions = 'ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|rss|atom|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|docx|xls|xlsx|exe|ppt|pptx|tar|mid|midi|wav|bmp|rtf';
                $array      = explode('|', $extensions);
                $temp       = end(explode('.', $action));
                if(in_array($temp, $array)) {
                    $action = PUBLIC_PATH.trim($action, '/');
                } else {
                    $action = ($action!='#') ? PUBLIC_PATH.trim($action, '/').'/' : '#';
                }
            }
        }
        if($icon) {
            $text = "<i class=\"fa fa-pd-expand $icon\"></i> $text";
        }
        return "<a href=\"$action\" $attrs >$text</a>";
    }


    /**
     * Método para generar un link tipo botón
     * @param string $action
     * @param string $text
     * @param array $attrs
     * @param string $icon
     * @param boolean $loadAjax
     * @return type
     */
    public static function button($action, $text = NULL, $attrs = array(), $icon='', $loadAjax = APP_AJAX) {
        if (is_array($attrs) OR empty($attrs)) {
            if(empty($attrs)) {
                $attrs['class'] = 'btn-info';
            }
            if($loadAjax) {
                if(empty($attrs['class'])) {
                    $attrs['class'] = 'js-link js-spinner js-url';
                } else {
                    if(!preg_match("/\bbtn-disabled\b/i", $attrs['class']) && !preg_match("/\bload-content\b/i", $attrs['class'])) {
                        if(!preg_match("/\bno-ajax\b/i", $attrs['class'])) {
                            $attrs['class'] = 'js-link '.$attrs['class'];
                        }
                        if(!preg_match("/\bno-spinner\b/i", $attrs['class'])) {
                            $attrs['class'] = 'js-spinner '.$attrs['class'];
                        }
                        if(!preg_match("/\bno-url\b/i", $attrs['class'])) {
                            $attrs['class'] = 'js-url '.$attrs['class'];
                        }
                    }
                }
            }
            $attrs['class'] = 'btn '.$attrs['class'];
            if(!preg_match("/\btext-bold\b/i", $attrs['class'])) {
                $attrs['class'] = $attrs['class'].' text-bold';
            }
            if(!empty($attrs)) {
                $attrs = Tag::getAttrs($attrs);
            }
        }

        if(!empty($action)) {
            $action = trim($action, '/').'/';
        }
        $text = (!empty($text) && $icon) ? '<span class="hidden-xs">'.Filter::get($text, 'upper').'</span>' : Filter::get($text, 'upper');
        if($icon) {
            $text = '<i class="btn-icon-only fa '.$icon.'"></i> '.$text;
        }
        if(empty($action) OR preg_match("/\bbtn-disabled\b/i", $attrs) OR preg_match("/\bload-content\b/i", $attrs)) {
            return "<button $attrs >$text</button>";
        }
        if(!preg_match('/^(http|ftp|https)\:\/\/+[a-z0-9\.\_-]+$/i', $action)) {
            return '<a href="' . PUBLIC_PATH . "$action\" $attrs >$text</a>";
        }
        return "<a href=\"$action\" $attrs >$text</a>";
    }



    /**
     * Crea un enlace externo
     *
     * @example echo DwHtml::outLink('http://kumbiaphp.com', 'Enlace') Crea un enlace a esa url
     *
     * @param string $action Ruta o dirección de la página web
     * @param string $text Texto a mostrar
     * @param mixed $attrs Atributos adicionales
     * @return string
     */
    public static function outLink($url, $text, $attrs=NULL) {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        return '<a href="' . "$url\" $attrs >$text</a>";
    }

    /**
     * Método para crear un ícono para las acciones del datagrid
     * @param string $action
     * @param array $attrs
     * @param string $type
     * @param strin $icon
     * @param boolean $loadAjax
     * @return string
     */
    public static function buttonTable($title, $action, $attrs = NULL, $type='info', $icon='fa-search', $loadAjax = APP_AJAX) {
        if(empty($attrs)) {
            $attrs = array();
            $attrs['class'] = "btn-small btn-$type";
        } else {
            $attrs['class'] = empty($attrs['class']) ? "btn-small btn-$type" : "btn-small btn-$type ".$attrs['class'];
        }
        $attrs['title'] = $title;
        $attrs['rel'] = 'tooltip';
        return self::button($action, '', $attrs, $icon, $loadAjax);
    }
}

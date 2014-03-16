<?php
/**
 *
 * Clase para el manejo de texto y otras cosas
 *
 * @package     Libs
 */

class DwUtils {

    /*
     * Metodo para obtener la ip real del cliente
     */
    public static function getIp() {
        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
            $entries = explode('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if ( preg_match("/^([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+)/", $entry, $ip_list) ) {
                    $private_ip = array('/^0\\./', '/^127\\.0\\.0\\.1/', '/^192\\.168\\..*/', '/^172\\.((1[6-9])|(2[0-9])|(3[0-1]))\\..*/', '/^10\\..*/');
                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
        }
        return $client_ip;
    }

    /*
     * Metodo para resaltar palabras de una cadena de texto
     */
    public static function resaltar($palabra, $texto) {
        $reemp  =   str_ireplace($palabra,'%s',$texto);
        $aux    =   $reemp;
        $veces  =   substr_count($reemp,'%s');
        if($veces == 0) {
            return $texto;
        }
        $palabras_originales    =   array();
        for($i = 0 ; $i < $veces ; $i ++) {
            $palabras_originales[] = '<b style="color: red;">'.substr($texto,strpos($aux,'%s'),strlen($palabra)).'</b>';
            $aux = substr($aux,0,strpos($aux,'%s')).$palabra.substr($aux,strlen(substr($aux,0,strpos($aux,'%s')))+2);
        }
        return vsprintf($reemp,$palabras_originales);
    }

    /**
     * Metodo para crear el slug de los titulos, categorias y etiquetas
     */
    public static function getSlug($string, $separator = '-', $length = 100) {
        $search = explode(',', 'ç,Ç,ñ,Ñ,æ,Æ,œ,á,Á,é,É,í,Í,ó,Ó,ú,Ú,à,À,è,È,ì,Ì,ò,Ò,ù,Ù,ä,ë,ï,Ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,Š,Œ,Ž,š,¥');
        $replace = explode(',', 'c,C,n,N,ae,AE,oe,a,A,e,E,i,I,o,O,u,U,a,A,e,E,i,I,o,O,u,U,ae,e,i,I,oe,ue,y,a,e,i,o,u,a,e,i,o,u,s,o,z,s,Y');
        $string = str_replace($search, $replace, $string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9_]/i', $separator, $string);
        $string = preg_replace('/\\' . $separator . '[\\' . $separator . ']*/', $separator, $string);
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }
        $string = preg_replace('/\\' . $separator . '$/', '', $string);
        $string = preg_replace('/^\\' . $separator . '/', '', $string);
        return $string;
    }

    /**
     * Método para ordenar un array de datos
     *
     * @param array $toOrderArray Array de datos
     * @param string $field Campo del array por el cual se va a ordenar
     * @param string $type Variable para indicar si ordena ASC o DESC
     * @return array
     */
    public static function orderArray($toOrderArray, $field, $type='DESC') {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key]  = $row[$field];
            $newRow[$key] = $row;
        }
        if ($type=='DESC') {
            arsort($position);
        } else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            $returnArray[] = $newRow[$key];
        }
        return $returnArray;
    }

    /**
     * Método que devuelve un array con las carpetas de un directorio
     */
    public static function getFolders($path) {
        $folders = array();
        if (is_dir($path)) {
            if ($ph = opendir($path)) {
                while (($source = readdir($ph)) !== false) {
                    if (is_dir($path . $source) && $source!="." && $source!="..") {
                        $folders[$source] = $source;
                    }
                }
                closedir($ph);
            }
        }
        return $folders;
    }

    /**
     * Escribe en letras un monto numerico
     *
     * @param numeric $valor
     * @param string $moneda
     * @param string $centavos
     * @return string
     */
    public static function getMoneyToLetter($valor, $moneda='PESOS', $centavos=0){
        $a = $valor;
        $p = $moneda;
        $c = $centavos;
        $val = "";
        $v = $a;
        $a = (int) $a;
        $d = round($v - $a, 2);
        if($a>=1000000){
            $val = millones($a - ($a % 1000000));
            $a = $a % 1000000;
        }
        if($a>=1000){
            $val.= miles($a - ($a % 1000));
            $a = $a % 1000;
        }
        $val.= trim(value_num($a))." $p ";
        if($d){
            $d*=100;
            $val.= "CON ".value_num($d)." $c ";
        }
        return $val;
    }

}

/**
 * Las siguientes funciones son utilizadas para la generación
 * de versiones escritas de numeros
 *
 * @param numeric $a
 * @return string
 */
function value_num($a){
    if($a<=21){
        switch ($a){
            case 1: return 'UNO';
            case 2: return 'DOS';
            case 3: return 'TRES';
            case 4: return 'CUATRO';
            case 5: return 'CINCO';
            case 6: return 'SEIS';
            case 7: return 'SIETE';
            case 8: return 'OCHO';
            case 9: return 'NUEVE';
            case 10: return 'DIEZ';
            case 11: return 'ONCE';
            case 12: return 'DOCE';
            case 13: return 'TRECE';
            case 14: return 'CATORCE';
            case 15: return 'QUINCE';
            case 16: return 'DIECISEIS';
            case 17: return 'DIECISIETE';
            case 18: return 'DIECIOCHO';
            case 19: return 'DIECINUEVE';
            case 20: return 'VEINTE';
            case 21: return 'VEINTIUN';
        }
    } else {
        if($a<=99){
            if($a>=22&&$a<=29) {
                return "VENTI".value_num($a % 10);
            }
            if($a==30) {
                return  "TREINTA";
            }
            if($a>=31&&$a<=39) {
                return "TREINTA Y ".value_num($a % 10);
            }
            if($a==40) {
                $b = "CUARENTA";
            }
            if($a>=41&&$a<=49) {
                return "CUARENTA Y ".value_num($a % 10);
            }
            if($a==50) {
                return "CINCUENTA";
            }
            if($a>=51&&$a<=59) {
                return "CINCUENTA Y ".value_num($a % 10);
            }
            if($a==60) {
                return "SESENTA";
            }
            if($a>=61&&$a<=69) {
                return "SESENTA Y ".value_num($a % 10);
            }
            if($a==70) {
                return "SETENTA";
            }
            if($a>=71&&$a<=79) {
                return "SETENTA Y ".value_num($a % 10);
            }
            if($a==80) {
                return "OCHENTA";
            }
            if($a>=81&&$a<=89) {
                return "OCHENTA Y ".value_num($a % 10);
            }
            if($a==90) {
                return "NOVENTA";
            }
            if($a>=91&&$a<=99) {
                return "NOVENTA Y ".value_num($a % 10);
            }
        } else {
            if($a==100) {
                return "CIEN";
            }
            if($a>=101&&$a<=199) {
                return "CIENTO ".value_num($a % 100);
            }
            if($a>=200&&$a<=299) {
                return "DOSCIENTOS ".value_num($a % 100);
            }
            if($a>=300&&$a<=399) {
                return "TRECIENTOS ".value_num($a % 100);
            }
            if($a>=400&&$a<=499) {
                return "CUATROCIENTOS ".value_num($a % 100);
            }
            if($a>=500&&$a<=599) {
                return "QUINIENTOS ".value_num($a % 100);
            }
            if($a>=600&&$a<=699) {
                return "SEICIENTOS ".value_num($a % 100);
            }
            if($a>=700&&$a<=799) {
                return "SETECIENTOS ".value_num($a % 100);
            }
            if($a>=800&&$a<=899) {
                return "OCHOCIENTOS ".value_num($a % 100);
            }
            if($a>=901&&$a<=999) {
                return "NOVECIENTOS ".value_num($a % 100);
            }
        }
    }
}
/**
 * Genera una cadena de millones
 *
 * @param numeric $a
 * @return string
 */
function millones($a) {
    $a = $a / 1000000;
    if($a==1) {
        return "UN MILLON ";
    } else {
        return value_num($a)." MILLONES ";
    }
}

/**
 * Genera una cadena de miles
 *
 * @param numeric $a
 * @return string
 */
function miles($a){
    $a = $a / 1000;
    if($a==1) {
	return "MIL";
    } else {
	return value_num($a)." MIL ";
    }
}


?>

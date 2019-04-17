<?php
/**
 * dfw_user: zerozelta
 * Date: 26/07/2018
 * Time: 10:25 AM
 */

namespace DFW;

class Utils{
    /**
     * Función que verifica que un email ingresado este escrito correctamente
     * @param $email string cadena a verificar si es un correo válido
     * @return boolean retorna true si es un correo válido, o false si es un correo escrito incorrectamente
     * @since 2.0.0
     */
    static function isValidEmail($email){
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Función que verifica si el sitio donde navega el usuario es un dispositivo movil
     * @return boolean si el usuario esta navegando un dispositivo movil devuelve un true,
     * de lo contrario devolvera un false
     * @since 2.0.0
     */
    static function isMobile(){
        $movile_data = "/ipod|iphone|ipad|android|opera mini|blackberry|palm os|windows ce|bada|windows phone|symbian|psp/i";
        if( preg_match($movile_data,strtolower($_SERVER['HTTP_USER_AGENT'])) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Comprueba si el cliente que hace la peticion es un Bot
     * @return Boolean verdadero o falso si el cliente es un bot
     */
    static function isBot($agent = null){
        if($agent == null){
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }

        return (
            isset($agent) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $agent)
        );
    }

    /**
     * Función que devuelve la IP del cliente
     * @return string con la IP del cliente que esta navegando
     * @since 2.0.0
     */
    static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function clearCache(){
        header('Expires: Sun, 01 Jan 1994 00:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    /**
     * Devuelve el identetificador del navegador con el cual se está navegando
     * @return string identificador del navegador actual
     * @since 2.0.0
     */
    static function getUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Función que obtiene los ficheros de un directorio
     * @param $dir string  es un String que contiene la dirección de la carpeta a buscar
     * @return array|null es un vector con todos los nombres (y extensión) de los ficheros encontrados en el directorio, incluyendo el nombre de carpetas. Puede devolver un falso en caso de no encontrar archivos.
     * @since 2.0.0
     */
    static function getFiles($dir){
        $fatmp = false;
        $fa = array();
        if(file_exists($dir)){
            @$fatmp = scandir($dir);
        }else{
            return null;
        }
        if($fatmp != false){
            $count = 0;
            for($i = 0;$i<count($fatmp);$i++){
                if($fatmp[$i] != "." && $fatmp[$i] != ".."){
                    $fa[$count] = $fatmp[$i];
                    $count = $count+1;
                }
            }

            natsort ($fa);

        }else{
            $fa = null;
        }
        return $fa;
    }

    /**
     * Sets a value in a nested array based on path
     * See https://stackoverflow.com/a/9628276/419887
     *
     * @param array $array The array to modify
     * @param string $path The path in the array
     * @param string $delimiter The separator for the path
     * @return object previous value
     */
    static function getValueFromPath($array, $path, $delimiter = '/') {
        $pathParts = explode($delimiter, $path);

        $current = $array;
        foreach($pathParts as $key) {
            if(isset($current[$key])){
                $current = $current[$key];
            }else {
                return null;
            }
        }

        return $current;
    }


    /**
     * Sets a value in a nested array based on path
     * See https://stackoverflow.com/a/9628276/419887
     *
     * @param array $array The array to modify
     * @param string $path The path in the array
     * @param mixed $value The value to set
     * @param string $delimiter The separator for the path
     * @return The previous value
     */
     static function setValueFromPath(&$array, $path, &$value, $delimiter = '/') {
        $pathParts = explode($delimiter, $path);

        $current = &$array;
        foreach($pathParts as $key) {
            $current = &$current[$key];
        }

        $backup = $current;
        $current = $value;

        return $backup;
    }

    /**
     * Función que elimina recursivamente los archivos de un directorio, así como los subdirectorios y sus
     * contenidos.
     * @param $dir string Es la ruta de la carpeta que será eliminada
     */
    static function rDeleteDir($dir) {
        if (is_dir($dir)){
            $objects = scandir($dir);
            foreach ($objects as $object){
                if ($object != "." && $object != ".."){
                    if (is_dir($dir."/".$object)){
                        rmdir($dir."/".$object);
                    }else{
                        unlink($dir."/".$object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    /**
     * Recive un array y una cadena de valores separados por coma, la función comprobará en el array si todos los valores están definidos dentro del array
     * @return Boolean Verdadero o falso si se encuentran seteados TODOS los valores dentro del array
     */
    public static function isDataSet($array,$values){
        $aval = $values;
        if(is_string($values)){
            $aval = explode(",",$values);
        }

        for($i = 0;$i < count($aval) ; $i++){
            $v = ($aval[$i]);
            if(!isset($array["{$v}"])){
                return false;
            }
        }
        return true;
    }

    public static function getRandomString($length = 8,$charset="abcdefghijklmnopqrstuvwxyz0123456789"){
        $string = '';
        $max = strlen($charset) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $charset[mt_rand(0, $max)];
        }

        return $string;
    }

    /**
     * @param $text
     * @param string $key
     * @return string
     */
    public static function encrypt($text,$key){
        return openssl_encrypt ($text, "aes128", $key);
    }

    /**
     * @param $cryptext
     * @param string $key
     * @return string
     */
    public static function decrypt($cryptext,$key){
        return openssl_encrypt ($cryptext, "aes128", $key);
    }

    /**
     * @param $text
     * @param bool $strip_tags
     * @return string
     */
    public static function sanitize($text,$strip_tags = true){
        if($strip_tags){ $text = strip_tags($text); }

        $text = trim($text);

        return $text;
    }

    /**
     * @param $array
     * @param bool $strip_tags
     */
    public static function sanitizeAll($array,$strip_tags = true){
        $res = array();
        $keys = array_keys($array);

        foreach ($keys as $key){
            $item = $array[$key];

            if(is_string($item)){
                $res[$key] = self::sanitize($item,$strip_tags);
            }else{
                $res[$key] = $item;
            }
        }

        return $res;
    }

    /**
     * @param $text
     * @param string $delimiter
     * @return string
     */
    public static function toSlug($text,$delimiter = "-"){
        return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $text))))), $delimiter));
    }


    /**
     * @param $input
     * @return string
     */
    public static function UTF8(&$input){
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::UTF8($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                self::UTF8($input->$var);
            }
        }

        return $input;
    }
}
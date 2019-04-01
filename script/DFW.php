<?php
/**
 * dfw_user: zerozelta
 * Date: 02/08/2018
 * Time: 03:35 PM
 */

use DFW\ConfigManager;
use DFW\DatabaseManager;
use DFW\SessionManager;

/**
 * Class DFW
 * @package DFW
 */
class DFW{

    private static $exitCallbacks = array();
    private static $jsonQueue = []; // JSON Object queue for makeJSON

    /**
     * initialice module config
     */
    public static function initialiceConfigSystem($config = "general.json"){
        ConfigManager::initialice($config);
    }

    /**
     * initialice module config
     */
    public static function initialiceDatabaseSystem() {
        DatabaseManager::initialice();
    }

    /**
     * initialice module config
     */
    public static function initialiceSessionSystem($sid = "sid",$stk = "stk") {
        SessionManager::initialice($sid,$stk);
    }

    /**
     * Initialice all the module instances with the default config
     */
    public static function initialice(){
        self::initialiceConfigSystem();
        self::initialiceDatabaseSystem();
        self::initialiceSessionSystem();
    }

    /**
     * Register an exit callback function in DFW
     * @param $function
     */
    public static function registerExitCallback($function){
        self::$exitCallbacks[] = $function;
    }

    public static function normalizePath($path) {
        $patterns = array('~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~');
        $replacements = array('/', '/', '', '');
        return str_replace("\\","/",preg_replace($patterns, $replacements, $path));
    }

    /**
     * @param $className
     */
    public static function autoload($className){
        if (substr($className, 0, 4) == "DFW\\") { // IS DFW classes
            $path = substr($className, 4, strlen($className));
            include_once(self::normalizePath(DFW_ROOT . "/script/" . $path . ".php"));
        }else{
            $path = DFW_ROOT . "/lib/" . $className . ".php";
            $path = self::normalizePath(str_replace("\\","/",$path));

            if(file_exists($path)){
                include($path);
            }
        }
    }

    /**
     *
     */
    public static function finalize(){
        foreach (self::$exitCallbacks as $cb) {
            $cb();
        }
    }

    /**
     * @return float|int
     */
    public static function getExecutionTime(){
        return (microtime(true) - DFW_TIME_START) * 1000;
    }

    /**
     * @param string $connectionName
     * @return DFW\Database|null
     */
    public static function DATABASE($connectionName = "default"){
        return DFW\DatabaseManager::get($connectionName);
    }

    /**
     * @return
     */
    public static function SESSION_USER(){
        return DFW\SessionManager::getUser();
    }

    /**
     * @param $userIdentifier
     * @return DFW\model\dfw_user|null
     */
    public static function USER($userIdentifier){
        return DFW\model\dfw_user::getUser($userIdentifier);
    }

    /**
     * search in default config script for an entry
     * @param $key
     * @param null $defaultValue
     * @return null|object|mixed
     */
    public static function cfg($key,$defaultValue = null){

        if(DFW\ConfigManager::$GENERAL == null){
            return $defaultValue;
        }

        return DFW\ConfigManager::$GENERAL->get($key,$defaultValue);
    }

    /**
     * @return bool
     */
    public static function isLogged(){
        return DFW\SessionManager::isLogged();
    }

    /**
     * Estandarización del empaquetado de objetos JSON de salida para cinder
     * Finaliza la ejecución del script
     * @param $status string estado de la peticion "success" para una ejecución exitosa | cualquier otro valor en aso contrario
     * @param $obj array array asociativo que contiene los datos del JSON a ser representado
     */
    public static function makeJSON($status, $obj = null, $debug = false){
        if($obj == null){ $obj = array(); }

        if(is_bool($status)){ // Si es bool el status se toma como success o error respectivamente
            if($status){
                $status = "success";
            }else{
                $status = "error";
            }
        }else if(is_array($status)){
            $status = true;
            $obj = $status;
        }

        $obj = array_merge(self::$jsonQueue,(array)$obj);    // Añadimos los registros en cola

        if($debug === true){
            if(isset($obj["_debug"]) == false){ $obj["_debug"] = []; }

            $obj["_debug"] = array_merge($obj["_debug"],array(
                "loadTime" => number_format(((microtime(true) - DFW_TIME_START) * 1000)),
                "SQL" => DFW\DatabaseManager::getQueryDebubLog(),
            ));
        }

        $obj["status"]  = $status;

        /// Headers

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

        if($status === "success"){
            header($protocol . ' 200 OK');
        }else{
            header($protocol . ' 400 ' . $status);
        }

        header("Content-type: application/json; charset=utf-8");
        exit(json_encode($obj,JSON_UNESCAPED_UNICODE)); // Finalizamos la ejecución del script
    }

    /**
     * Añade campos al JSON final pero sin terminar la ejecución, estos datos se apilan y los campos con el mismo nombre
     * se sobreescriben dando preferencia al ultimo campo seteado con addJSON
     * @param $spaceName string Nombre del campo al que será integrado el campo
     * @param array $jsonArray array
     */
    public static function addJSON($spaceName,$jsonArray = []){
        if(!isset(self::$jsonQueue[$spaceName])){
            self::$jsonQueue[$spaceName] = [];
        }
        if(is_array($jsonArray)){
            self::$jsonQueue[$spaceName] = array_merge(self::$jsonQueue[$spaceName],$jsonArray);
        }else{
            self::$jsonQueue[$spaceName] = $jsonArray;
        }
    }

}

spl_autoload_register('DFW::autoload');
register_shutdown_function ('DFW::finalize');

include (DFW_ROOT . "/lib/Illuminate/Support/helpers.php"); // Helpers de illumminate
<?php
/**
 * User: zerozelta
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

    /**
     * @param $className
     */
    public static function autoload($className){
        if (substr($className, 0, 4) == "DFW\\") { // IS DFW classes
            $path = substr($className, 4, strlen($className));
            include_once(DFW_ROOT . "/script/" . $path . ".php");
        }else{
            $path = DFW_ROOT_DIR . "/lib/" . $className . ".php";
            $path = str_replace("\\","/",$path);

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
     * @return DFW\model\User|null
     */
    public static function USER($userIdentifier){
        return DFW\model\User::getUser($userIdentifier);
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

}


spl_autoload_register('DFW::autoload');
register_shutdown_function ('DFW::finalize');
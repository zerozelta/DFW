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
    public static function initialiceConfigSystem($config = "general.php"){
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
     * @return DFW\session\User|null
     */
    public static function USER($userIdentifier){
        return DFW\session\User::getUser($userIdentifier);
    }

    /**
     * @return bool
     */
    public static function isLogged(){
        return DFW\SessionManager::isLogged();
    }

}


spl_autoload_register('DFW\System::autoload');
register_shutdown_function ('DFW\System::
');
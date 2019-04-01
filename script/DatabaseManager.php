<?php
/**
 * dfw_user: zerozelta
 * Date: 26/07/2018
 * Time: 04:08 PM
 */

namespace DFW;

use Illuminate\Database\Capsule\Manager as Capsule;
use DFW\ConfigManager;
use DFW\Database;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;


class DatabaseManager{

    /**
     * @var Capsule
     */
    public static $CAPSULE;
    private static $queryDebug = true;

    private static $databases= array();
    private static $queryLog = array();

    public static function initialice(){
        self::$CAPSULE = new Capsule();

        $dbcfg = ConfigManager::$GENERAL->get("DATABASE");
        self::connect("default",$dbcfg);

        self::$CAPSULE->setAsGlobal();
        self::$CAPSULE->bootEloquent();

    }


    /**
     * @param $name string name
     * @return Database|null database registered asociated to this connection name
     */
    public static function get($name = "default"){
        return self::$databases[$name];
    }

    /**
     * @param $connection string
     * @param array $config Object with dbconfiguration or array of objects for multidatabase
     * @return Database|null
     */
    public static function connect($connection = "default",$config){
        if($config == null){
            return null;
        }

        $cfgk = array_keys($config);
        if(gettype($config[$cfgk[0]]) == "array"){
            for($i = 0;$i<count($config);$i++){
                $key = array_keys($config)[$i];
                self::connect($key,$config[$cfgk[$i]]);
            }
            return null;
        }

        self::$CAPSULE->addConnection($config,$connection);

        $conObj = self::$CAPSULE->getConnection($connection);
        $conObj->setEventDispatcher(new Dispatcher());
        $conObj->listen(
            function ($q){
                if(self::$queryDebug == true){
                    self::addQueryDebugRecord($q->sql,$q->time,$q->connection->getPdo()->errorInfo()[2],$q->connectionName);
                }
            }
        );

        $db = new Database($conObj);
        self::$databases[$connection] = $db;

        return $db;
    }

    /**
     * Enables the query debug mode in database globally
     */
    public static function enableQueryDebug(){
        self::$queryDebug = true;
    }

    /**
     * Disables the query debug mode in database globally
     */
    public static function disableQueryDebug(){
        self::$queryDebug = false;
    }

    /**
     * return the queryLog onli when queryDebug is enable
     * @return array
     */
    public static function getQueryDebubLog(){
        return self::$queryLog;
    }

    public static function addQueryDebugRecord($sql,$time,$error,$connection = "default"){
        self::$queryLog[] = array("sql" => $sql , "time" => $time , "SQLError" => $error , "connection" => $connection);
    }


    /**
     * Install all DFW structs if not exists
     * @param string $db string|Database
     * @return bool true|false if DFW structs are created
     */
    public static function installDFWStructs($db = "default",$transaction = true){
        global $DFWStructs;

        if(is_string($db)){
            $db = self::get($db);
        }

        if($db == null || $db instanceof Database == false){
            return false;
        }

        include(DFW_ROOT . "/script/model/DFWStructs.php");

        try{
            if($transaction){ $db->getConnection()->beginTransaction(); }

            $keys = array_keys($DFWStructs);
            foreach ($keys AS $key){
                $tqb = $DFWStructs[$key];
                if (!$db->getSchema()->hasTable($key)) {
                    $db->getSchema()->create($key, $tqb);
                }
            }

            if($transaction){ $db->getConnection()->commit(); }
        }catch (\Exception $e){
            if($transaction){ $db->getConnection()->rollBack(); }
            return false;
        }

        return true;
    }

}
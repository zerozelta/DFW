<?php
/**
 * User: zerozelta
 * Date: 26/07/2018
 * Time: 10:01 AM
 */

namespace DFW;

use DFW\Utils;

/**
 * Class ConfigScript
 * Handle jsson scripts (generally from )
 */
class ConfigScript{

    private $cfg = array();  // configuration array
    private $path;

    /**
     * ConfigScript constructor.
     * @param null $filename file name of json config file (in config directory)
     */
    public function __construct($filename = "general.json"){
        if(is_array($filename)){
            $this->cfg = $filename;
        }else{
            $this->path = DFW_ROOT . "/config/". $filename;
            $this->reload();
        }
    }

    /**
     * reloads the configuration from file (all changes that wasn't saved will be reloaded)
     */
    public function reload(){

        if($this->path == null || !file_exists($this->path)){
            return $this->cfg;
        }

        $text = file_get_contents($this->path);
        $json = json_decode($text, true);

        $this->cfg = $json;
        return $this->cfg;
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return null|object
     */
    public function get($key,$defaultValue = null){
        $var = Utils::getValueFromPath($this->cfg,$key);
        if($var == null || empty($var)){
            return $defaultValue;
        }else{
            return $var;
        }
    }

    /**
     *
     * @return array
     */
    public function getConfig(){
        return $this->cfg;
    }

    /**
     * @param $key string|array
     * @param $value
     */
    public function set($key,$value = null){
        if(is_array($key)){
            $this->cfg = $key;
        }else{
            Utils::setValueFromPath($this->cfg,$key,$value);
        }
    }

    /**
     * @param $arrayCfg array
     */
    public function save($arrayCfg = null){
        if($this->path == null){
            return false;
        }

        if($arrayCfg == null){
            $arrayCfg = $this->cfg;
        }

        $text = json_encode($arrayCfg,JSON_PRETTY_PRINT);
        if(!empty($text)){
            file_put_contents($this->path, $text);
            $this->cfg = $arrayCfg;
        }

        return true;
    }
}